<?php

namespace App\Jobs;

use App\Models\Email;
use App\Models\EmailAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Webklex\IMAP\Facades\Client;

class DownloadEmailsForAccount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $account;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(EmailAccount $account)
    {
        $this->account = $account;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Increase memory limit for this job
        ini_set('memory_limit', '512M');
        
        try {
            $client = Client::make([
                'host'          => $this->account->imap_host,
                'port'          => $this->account->imap_port,
                'encryption'    => $this->account->imap_encryption, 
                'validate_cert' => false,
                'username'      => $this->account->imap_user,
                'password'      => $this->account->imap_pass,
                'protocol'      => 'imap'
            ]);

            \Log::info('Downloading emails for account:', [
                'id' => $this->account->id,
                'email_address' => $this->account->email_address,
                'imap_host' => $this->account->imap_host,
                'imap_port' => $this->account->imap_port,
                'imap_encryption' => $this->account->imap_encryption,
                'imap_user' => $this->account->imap_user
            ]);

            $client->connect();

            
            $folder = $client->getFolder('INBOX');
            \Log::info('Connected to INBOX folder');

            $since = new \DateTime('2024-11-20');
            \Log::info('Searching for emails since:', ['date' => $since->format('Y-m-d H:i:s')]);
            
            try {
                \Log::info('Fetching messages');
                
                $messages = $folder->messages()
                    ->since($since)
                    ->all()
                    ->get();
                
                \Log::info('Retrieved messages count:', ['count' => count($messages)]);

                foreach ($messages as $message) {
                    try {
                        $decodedSubject = iconv_mime_decode($message->getSubject(), 0, 'UTF-8');
                        \Log::info('Processing email:', ['subject' => $decodedSubject]);
                        
                        // Safely get 'from' address
                        $fromAddresses = $message->getFrom();
                        $fromEmail = null;
                        if (!empty($fromAddresses) && isset($fromAddresses[0]) && $fromAddresses[0]) {
                            $fromEmail = $fromAddresses[0]->mail ?? null;
                        }
                        
                        // Safely get 'to' address
                        $toAddresses = $message->getTo();
                        $toEmail = null;
                        if (!empty($toAddresses) && isset($toAddresses[0]) && $toAddresses[0]) {
                            $toEmail = $toAddresses[0]->mail ?? null;
                        }

                        // Skip if we don't have a valid from address or to address (likely spam)
                        if (!$fromEmail || !$toEmail) {
                            \Log::warning('Skipping email with missing from or to address', [
                                'subject' => $decodedSubject,
                                'date' => $message->getDate(),
                                'from' => $fromEmail,
                                'to' => $toEmail
                            ]);
                            continue;
                        }

                        Email::updateOrCreate(
                            [
                                'subject' => $decodedSubject, 
                                'received_at' => $message->getDate(),
                                'email_account_id' => $this->account->id
                            ],
                            [
                                'from'             => $fromEmail,
                                'to'               => $toEmail,
                                'subject'          => $decodedSubject,
                                'body'             => $message->getTextBody(),
                                'received_at'      => $message->getDate(),
                                'email_account_id' => $this->account->id
                            ]
                        );
                        
                        // Clear some memory after each email
                        gc_collect_cycles();
                        
                    } catch (\Exception $e) {
                        \Log::error('Failed to process email:', [
                            'subject' => $decodedSubject ?? 'unknown',
                            'error' => $e->getMessage()
                        ]);
                    }
                }
                
            } catch (\Exception $e) {
                \Log::error('Failed to fetch messages:', [
                    'error' => $e->getMessage()
                ]);
            }
            
        } catch (\Exception $e) {
            \Log::error('Failed to download emails for account:', [
                'id' => $this->account->id,
                'email_address' => $this->account->email_address,
                'error' => $e->getMessage()
            ]);

            // Send a Telegram message about the account failure
            $telegramMessage = "Failed to connect to email account: " . $this->account->email_address;
            // Assuming you have a function to send Telegram messages
            $this->sendTelegramMessage($telegramMessage);
        }
    }
    // Add this method to handle sending Telegram messages
    protected function sendTelegramMessage($message)
    {
        try {
            $telegramController = new \App\Http\Controllers\TelegramController();
            return $telegramController->sendMessage('Email Account Alert', $message);
        } catch (\Exception $e) {
            \Log::error('Failed to send Telegram message:', [
                'error' => $e->getMessage(),
                'message' => $message
            ]);
        }
    }
}

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
        try {
            $client = Client::make([
                'host'          => $this->account->imap_host,
                'port'          => $this->account->imap_port,
                'encryption'    => $this->account->imap_encryption, 
                'validate_cert' => true,
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
            $since = new \DateTime('2024-11-20');
            $messages = $folder->messages()
                ->since($since)
                ->all()
                ->get();

            foreach ($messages as $message) {
                $decodedSubject = iconv_mime_decode($message->getSubject(), 0, 'UTF-8');

                Email::updateOrCreate(
                    [
                        'subject' => $decodedSubject, 
                        'received_at' => $message->getDate(),
                        'email_account_id' => $this->account->id
                    ],
                    [
                        'from'             => $message->getFrom()[0]->mail,
                        'to'               => $message->getTo()[0]->mail ?? null,
                        'subject'          => $decodedSubject,
                        'body'             => $message->getTextBody(),
                        'received_at'      => $message->getDate(),
                        'email_account_id' => $this->account->id
                    ]
                );
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
        // Implement your Telegram API call here
        // Example: Telegram::sendMessage($message);
    }
}

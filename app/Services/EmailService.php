<?php

namespace App\Services;

use App\Models\EmailAccount;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class EmailService
{
    private $emailAccount;
    private $emailAddress;

    public function __construct(EmailAccount $emailAccount)
    {
        $this->emailAccount = $emailAccount;
        $this->emailAddress = trim($emailAccount->email_address);
        $this->configureMailer();
    }

    private function configureMailer()
    {
        // Configure SMTP settings for this email
        Config::set('mail.mailers.smtp.host', $this->emailAccount->smtp_host);
        Config::set('mail.mailers.smtp.port', $this->emailAccount->smtp_port);
        Config::set('mail.mailers.smtp.username', $this->emailAddress);
        Config::set('mail.mailers.smtp.password', trim($this->emailAccount->smtp_pass));
        Config::set('mail.mailers.smtp.encryption', strtolower($this->emailAccount->smtp_encryption));
        Config::set('mail.from.address', $this->emailAddress);
        Config::set('mail.from.name', $this->emailAccount->name ?? $this->emailAddress);

        // Log SMTP settings (excluding sensitive info)
        Log::info('SMTP settings initialized:', [
            'host' => $this->emailAccount->smtp_host,
            'port' => $this->emailAccount->smtp_port,
            'encryption' => strtolower($this->emailAccount->smtp_encryption),
            'username' => $this->emailAddress,
            'pass' => $this->emailAccount->smtp_pass
        ]);
    }

    public function sendEmail($to, $subject, $body, $cc = null)
    {
        try {
            // Send email with HTML content
            $result = Mail::html($body, function($message) use ($to, $subject, $cc) {
                $message->from($this->emailAddress, $this->emailAccount->name ?? $this->emailAddress)
                        ->to($to)
                        ->subject($subject);

                if (!empty($cc)) {
                    $message->cc($cc);
                }
            });

            Log::info("Email sent successfully from {$this->emailAddress} to {$to}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send email: " . $e->getMessage(), [
                'from' => $this->emailAddress,
                'to' => $to,
                'subject' => $subject
            ]);
            throw $e;
        }
    }
}

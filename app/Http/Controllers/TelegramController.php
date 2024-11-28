<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class TelegramController extends Controller
{
    protected $telegramBotApi;
    protected $telegramGroupId;

    public function __construct()
    {
        $this->telegramBotApi = env('TELEGRAM_BOT_API', '');
        $this->telegramGroupId = env('TELEGRAM_GROUP_ID', '');
    }

    public function sendMessage($subject, $body)
    {
        if (empty($this->telegramBotApi) || empty($this->telegramGroupId)) {
            Log::error('Telegram configuration missing');
            return false;
        }

        try {
            $telegramMessage = [
                'chat_id' => $this->telegramGroupId,
                'text' => "🔔 *{$subject}*\n\n{$body}",
                'parse_mode' => 'Markdown'
            ];

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => "https://api.telegram.org/bot{$this->telegramBotApi}/sendMessage",
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => http_build_query($telegramMessage),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_TIMEOUT => 10
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                Log::error('Telegram API curl error', ['error' => $error]);
                return false;
            }

            if ($httpCode !== 200) {
                Log::error('Telegram API error', [
                    'httpCode' => $httpCode,
                    'response' => $response
                ]);
                return false;
            }

            Log::info('Telegram message sent successfully', [
                'subject' => $subject,
                'httpCode' => $httpCode
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send Telegram message', [
                'error' => $e->getMessage(),
                'subject' => $subject
            ]);
            return false;
        }
    }
}
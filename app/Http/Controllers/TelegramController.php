<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;

class TelegramController extends Controller
{
    protected $telegramBotApi;
    protected $telegramGroupId;

    public function __construct()
    {

        $this->telegramBotApi = '1790397670:AAFsAoJ5Qju9V4phdq5cflhJF938uS7t2QI';
        $this->telegramGroupId = '-1002216138405';
    }

    public function sendMessage($subject, $body)
    {
        $telegramMessage = [
            'chat_id' => $this->telegramGroupId,
            'text' => "Subject: {$subject}\n\nBody:\n{$body}"
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot{$this->telegramBotApi}/sendMessage");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($telegramMessage));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        //Log::info("Sent message to Telegram group", ['response' => $response]);

        return $response;
    }
}
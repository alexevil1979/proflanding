<?php

declare(strict_types=1);

namespace App\Core;

final class Telegram
{
    public function sendMessage(string $text, ?string $chatId = null): array
    {
        $token = (string)Config::get('telegram.token', '');
        $chat = $chatId ?: (string)Config::get('telegram.chat_id', '');
        if ($token === '' || $chat === '') {
            return ['ok' => false, 'response' => 'Telegram не настроен'];
        }

        $url = 'https://api.telegram.org/bot' . $token . '/sendMessage';
        $payload = http_build_query([
            'chat_id' => $chat,
            'text' => $text,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => '1',
        ]);

        $ctx = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'content' => $payload,
                'timeout' => 15,
                'ignore_errors' => true,
            ],
        ]);

        $raw = @file_get_contents($url, false, $ctx);
        if ($raw === false) {
            return ['ok' => false, 'response' => 'HTTP request failed'];
        }
        $json = json_decode($raw, true);
        if (!is_array($json)) {
            return ['ok' => false, 'response' => substr($raw, 0, 500)];
        }
        if (!empty($json['ok'])) {
            return ['ok' => true, 'response' => 'ok'];
        }
        return ['ok' => false, 'response' => $raw];
    }
}

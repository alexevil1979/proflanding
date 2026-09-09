<?php

declare(strict_types=1);

namespace App\Core;

final class Telegram
{
    public function sendMessage(string $text, ?string $chatId = null): array
    {
        $cfg = \App\Services\NotifyConfig::telegram();
        $token = (string)($cfg['token'] ?? '');
        $chat = $chatId ?: (string)($cfg['chat_id'] ?? '');
        if ($token === '' || $chat === '') {
            return ['ok' => false, 'response' => 'Telegram не настроен'];
        }

        $url = 'https://api.telegram.org/bot' . $token . '/sendMessage';
        $payload = [
            'chat_id' => $chat,
            'text' => $text,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => '1',
        ];

        $raw = $this->request($url, $payload, $cfg['proxy'] ?? []);
        if ($raw === false) {
            return ['ok' => false, 'response' => 'HTTP request failed (проверьте сеть/прокси)'];
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

    /**
     * @param array<string, mixed> $payload
     * @param array{enabled?:bool,type?:string,host?:string,port?:int,user?:string,pass?:string} $proxy
     */
    private function request(string $url, array $payload, array $proxy): string|false
    {
        $body = http_build_query($payload);
        $useProxy = !empty($proxy['enabled']) && !empty($proxy['host']) && !empty($proxy['port']);

        if (function_exists('curl_init')) {
            return $this->requestCurl($url, $body, $useProxy ? $proxy : null);
        }

        return $this->requestStream($url, $body, $useProxy ? $proxy : null);
    }

    private function requestCurl(string $url, string $body, ?array $proxy): string|false
    {
        $ch = curl_init($url);
        if ($ch === false) {
            return false;
        }

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);

        if ($proxy !== null) {
            $host = (string)$proxy['host'];
            $port = (int)$proxy['port'];
            $type = strtolower((string)($proxy['type'] ?? 'socks5'));
            curl_setopt($ch, CURLOPT_PROXY, $host . ':' . $port);
            curl_setopt($ch, CURLOPT_PROXYTYPE, match ($type) {
                'http' => CURLPROXY_HTTP,
                'https' => defined('CURLPROXY_HTTPS') ? CURLPROXY_HTTPS : CURLPROXY_HTTP,
                'socks4' => CURLPROXY_SOCKS4,
                'socks5h' => defined('CURLPROXY_SOCKS5_HOSTNAME') ? CURLPROXY_SOCKS5_HOSTNAME : CURLPROXY_SOCKS5,
                default => CURLPROXY_SOCKS5,
            });
            $user = (string)($proxy['user'] ?? '');
            $pass = (string)($proxy['pass'] ?? '');
            if ($user !== '') {
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $user . ':' . $pass);
            }
        }

        $raw = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($raw === false) {
            Logger::error('Telegram curl failed', ['error' => $err]);
            return false;
        }
        return (string)$raw;
    }

    private function requestStream(string $url, string $body, ?array $proxy): string|false
    {
        $opts = [
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'content' => $body,
                'timeout' => 30,
                'ignore_errors' => true,
            ],
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true,
            ],
        ];

        // Без curl stream поддерживает в основном HTTP(S)-прокси
        if ($proxy !== null) {
            $type = strtolower((string)($proxy['type'] ?? 'http'));
            if (!in_array($type, ['http', 'https'], true)) {
                Logger::error('Telegram SOCKS proxy requires php-curl');
                return false;
            }
            $auth = '';
            $user = (string)($proxy['user'] ?? '');
            $pass = (string)($proxy['pass'] ?? '');
            if ($user !== '') {
                $auth = rawurlencode($user) . ':' . rawurlencode($pass) . '@';
            }
            $opts['http']['proxy'] = 'tcp://' . $auth . $proxy['host'] . ':' . (int)$proxy['port'];
            $opts['http']['request_fulluri'] = true;
        }

        $ctx = stream_context_create($opts);
        $raw = @file_get_contents($url, false, $ctx);
        return $raw === false ? false : $raw;
    }
}

<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Минимальный SMTP-клиент для Gmail (STARTTLS + AUTH LOGIN).
 */
final class Mailer
{
    public function send(string $to, string $subject, string $html, string $text = ''): array
    {
        $cfg = Config::get('smtp', []);
        $host = (string)($cfg['host'] ?? 'smtp.gmail.com');
        $port = (int)($cfg['port'] ?? 587);
        $user = (string)($cfg['user'] ?? '');
        $pass = (string)($cfg['pass'] ?? '');
        $from = (string)($cfg['from'] ?? $user);
        $fromName = (string)($cfg['from_name'] ?? 'IT Specialist');

        if ($to === '' || $user === '' || $pass === '' || $from === '') {
            return ['ok' => false, 'response' => 'SMTP не настроен'];
        }

        if ($text === '') {
            $text = trim(strip_tags(str_replace(['<br>', '<br/>', '<br />', '</p>'], "\n", $html)));
        }

        try {
            $errno = 0;
            $errstr = '';
            $fp = stream_socket_client(
                "tcp://{$host}:{$port}",
                $errno,
                $errstr,
                20,
                STREAM_CLIENT_CONNECT
            );
            if (!$fp) {
                return ['ok' => false, 'response' => "Connect failed: {$errstr}"];
            }
            stream_set_timeout($fp, 20);

            $this->expect($fp, 220);
            $this->cmd($fp, 'EHLO localhost', 250);
            $this->cmd($fp, 'STARTTLS', 220);
            if (!stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                fclose($fp);
                return ['ok' => false, 'response' => 'STARTTLS failed'];
            }
            $this->cmd($fp, 'EHLO localhost', 250);
            $this->cmd($fp, 'AUTH LOGIN', 334);
            $this->cmd($fp, base64_encode($user), 334);
            $this->cmd($fp, base64_encode($pass), 235);
            $this->cmd($fp, 'MAIL FROM:<' . $from . '>', 250);
            $this->cmd($fp, 'RCPT TO:<' . $to . '>', 250);
            $this->cmd($fp, 'DATA', 354);

            $boundary = 'b_' . bin2hex(random_bytes(8));
            $headers = [];
            $headers[] = 'Date: ' . date('r');
            $headers[] = 'From: ' . $this->encodeAddress($fromName, $from);
            $headers[] = 'To: <' . $to . '>';
            $headers[] = 'Subject: ' . $this->encodeHeader($subject);
            $headers[] = 'MIME-Version: 1.0';
            $headers[] = 'Content-Type: multipart/alternative; boundary="' . $boundary . '"';
            $headers[] = 'Message-ID: <' . bin2hex(random_bytes(12)) . '@proflanding>';

            $body = '--' . $boundary . "\r\n";
            $body .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n" . $text . "\r\n";
            $body .= '--' . $boundary . "\r\n";
            $body .= "Content-Type: text/html; charset=UTF-8\r\n\r\n" . $html . "\r\n";
            $body .= '--' . $boundary . "--\r\n.";

            fwrite($fp, implode("\r\n", $headers) . "\r\n\r\n" . $body . "\r\n");
            $this->expect($fp, 250);
            $this->cmd($fp, 'QUIT', 221);
            fclose($fp);

            return ['ok' => true, 'response' => 'sent'];
        } catch (\Throwable $e) {
            Logger::error('SMTP error', ['message' => $e->getMessage()]);
            return ['ok' => false, 'response' => $e->getMessage()];
        }
    }

    private function cmd($fp, string $cmd, int $code): void
    {
        fwrite($fp, $cmd . "\r\n");
        $this->expect($fp, $code);
    }

    private function expect($fp, int $code): void
    {
        $response = '';
        while (($line = fgets($fp, 515)) !== false) {
            $response .= $line;
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
        }
        if (!str_starts_with(trim($response), (string)$code)) {
            throw new \RuntimeException('SMTP unexpected: ' . trim($response));
        }
    }

    private function encodeHeader(string $value): string
    {
        return '=?UTF-8?B?' . base64_encode($value) . '?=';
    }

    private function encodeAddress(string $name, string $email): string
    {
        return $this->encodeHeader($name) . ' <' . $email . '>';
    }
}

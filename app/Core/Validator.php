<?php

declare(strict_types=1);

namespace App\Core;

final class Validator
{
    /** @var array<string, string> */
    private array $errors = [];

    public function __construct(private array $data)
    {
    }

    public function required(string $field, string $label): self
    {
        $v = trim((string)($this->data[$field] ?? ''));
        if ($v === '') {
            $this->errors[$field] = "Заполните поле «{$label}»";
        }
        return $this;
    }

    public function email(string $field, string $label, bool $optional = true): self
    {
        $v = trim((string)($this->data[$field] ?? ''));
        if ($v === '' && $optional) {
            return $this;
        }
        if (!filter_var($v, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = "Некорректный {$label}";
        }
        return $this;
    }

    public function phone(string $field, string $label): self
    {
        $v = preg_replace('/[^\d+]/', '', (string)($this->data[$field] ?? '')) ?? '';
        if (strlen(preg_replace('/\D/', '', $v) ?? '') < 10) {
            $this->errors[$field] = "Некорректный {$label}";
        }
        return $this;
    }

    public function maxLen(string $field, int $max, string $label): self
    {
        $v = (string)($this->data[$field] ?? '');
        if (mb_strlen($v) > $max) {
            $this->errors[$field] = "{$label}: максимум {$max} символов";
        }
        return $this;
    }

    public function accepted(string $field, string $message): self
    {
        $v = $this->data[$field] ?? null;
        if (!$v) {
            $this->errors[$field] = $message;
        }
        return $this;
    }

    public function fails(): bool
    {
        return $this->errors !== [];
    }

    /** @return array<string, string> */
    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(): string
    {
        return $this->errors ? (string)reset($this->errors) : '';
    }
}

<?php

namespace Aslnbxrz\OneId\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\DataCollectionOf;

class OneIDAuthResult extends Data
{
    public bool $success;
    public string $message;
    public ?int $status = null;
    public ?array $data = null;
    public ?string $error = null;

    /**
     * OneID user data'ni olish
     */
    public function getUserData(): ?OneIDUserData
    {
        if (!$this->success || !$this->data) {
            return null;
        }

        try {
            return OneIDUserData::from($this->data);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Foydalanuvchi PIN'ini olish
     */
    public function getPin(): ?string
    {
        return $this->data['pin'] ?? null;
    }

    /**
     * Foydalanuvchi to'liq ismini olish
     */
    public function getFullName(): ?string
    {
        return $this->data['full_name'] ?? null;
    }

    /**
     * Foydalanuvchi ma'lumotlarini tekshirish
     */
    public function isValidUser(): bool
    {
        if (!$this->success || !$this->data) {
            return false;
        }

        $required = ['pin', 'first_name', 'last_name'];
        foreach ($required as $field) {
            if (empty($this->data[$field])) {
                return false;
            }
        }

        return true;
    }
}
<?php

namespace Aslnbxrz\OneId\Data;

use Spatie\LaravelData\Data;

class OneIDAuthResult extends Data
{
    public bool $success;

    public string $message;

    public ?string $token = null;

    public ?int $status = null;

    public ?array $data = null;

    public ?string $error = null;

    /**
     * OneID user data
     */
    public function getUserData(): ?OneIDUserData
    {
        if (! $this->success || ! $this->data) {
            return null;
        }

        try {
            return OneIDUserData::from($this->data);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * get user pin
     */
    public function getPin(): ?string
    {
        return $this->data['pin'] ?? null;
    }

    /**
     * get user full name
     */
    public function getFullName(): ?string
    {
        return $this->data['full_name'] ?? null;
    }

    /**
     * check if user is valid
     */
    public function isValidUser(): bool
    {
        if (! $this->success || ! $this->data) {
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

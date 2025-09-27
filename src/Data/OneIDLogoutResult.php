<?php

namespace Aslnbxrz\OneId\Data;

use Spatie\LaravelData\Data;

class OneIDLogoutResult extends Data
{
    public bool $success;
    public string $message;
    public ?int $status = null;
    public ?string $error = null;
}
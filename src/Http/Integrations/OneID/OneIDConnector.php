<?php

namespace Aslnbxrz\OneId\Http\Integrations\OneID;

use Illuminate\Support\Facades\Config;
use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;

class OneIDConnector extends Connector
{
    use AcceptsJson;

    /**
     * The Base URL of the API
     */
    public function resolveBaseUrl(): string
    {
        return config('oneid.base_url');
    }

    /**
     * Default headers for every request
     */
    protected function defaultHeaders(): array
    {
        return config('oneid.default_headers', []);
    }

    /**
     * Default HTTP client options
     */
    protected function defaultConfig(): array
    {
        return config('oneid.default_config', []);
    }
}

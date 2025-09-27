<?php

namespace Aslnbxrz\OneId\Http\Integrations\OneID\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class OneIDGetTokenRequest extends Request
{
    public function __construct(public string $code) {}

    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::POST;

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return config('oneid.endpoints.token', '/sso/oauth/Authorization.do');
    }

    protected function defaultQuery(): array
    {
        return [
            'grant_type' => 'one_authorization_code',
            'client_id' => config('oneid.client_id'),
            'client_secret' => config('oneid.client_secret'),
            'redirect_uri' => config('oneid.redirect_uri'),
            'code' => $this->code,
        ];
    }
}

<?php

namespace Aslnbxrz\OneId\Http\Integrations\OneID\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class OneIDLogoutRequest extends Request
{
    public function __construct(public string $accessToken){}

    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::POST;

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return config('oneid.endpoints.logout', '/sso/oauth/Authorization.do');
    }

    protected function defaultQuery(): array
    {
        return [
            'grant_type'    => 'one_log_out',
            'client_id'     => config('oneid.client_id'),
            'client_secret' => config('oneid.client_secret'),
            'scope'         => config('oneid.scope'),
            'access_token'  => $this->accessToken,
        ];
    }
}

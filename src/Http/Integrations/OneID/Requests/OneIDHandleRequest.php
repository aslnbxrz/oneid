<?php

namespace Aslnbxrz\OneId\Http\Integrations\OneID\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class OneIDHandleRequest extends Request
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
        return config('oneid.endpoints.user_info', '/sso/oauth/Authorization.do');
    }

    protected function defaultQuery(): array
    {
        return [
            'grant_type'    => 'one_access_token_identify',
            'client_id'     => config('oneid.client_id'),
            'client_secret' => config('oneid.client_secret'),
            'scope'         => config('oneid.scope'),
            'redirect_uri'  => config('oneid.redirect_uri'),
            'access_token'  => $this->accessToken,
        ];
    }
}

<?php

namespace Aslnbxrz\OneId;

use Aslnbxrz\OneId\Http\Integrations\OneID\OneIDConnector;
use Aslnbxrz\OneId\Http\Integrations\OneID\Requests\OneIDGetTokenRequest;
use Aslnbxrz\OneId\Http\Integrations\OneID\Requests\OneIDHandleRequest;
use Aslnbxrz\OneId\Http\Integrations\OneID\Requests\OneIDLogoutRequest;
use Aslnbxrz\OneId\Data\OneIDAuthResult;
use Aslnbxrz\OneId\Data\OneIDLogoutResult;
use Illuminate\Support\Facades\Log;
use JsonException;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;
use Saloon\Http\Request;
use Saloon\Http\Response;

final readonly class OneIDService
{
    /**
     * Public entry: exchange code -> fetch profile -> map to DTO.
     */
    public static function handle(string $code): OneIDAuthResult
    {
        $payload = self::oneIdHandle($code);

        // Always return a consistent DTO payload
        return OneIDAuthResult::from([
            'success' => $payload['success'] ?? false,
            'message' => $payload['message'] ?? 'Failed to authorize with OneID',
            'status' => $payload['status'] ?? null,
            'data' => $payload['data'] ?? null,
            'error' => $payload['error'] ?? null,
        ]);
    }

    /**
     * Logout with robust error handling.
     */
    public static function logout(string $oneIDAccessToken): OneIDLogoutResult
    {
        try {
            self::send(new OneIDLogoutRequest($oneIDAccessToken));

            return OneIDLogoutResult::from([
                'success' => true,
                'message' => 'Logged out successfully',
                'status' => 200,
                'error' => null,
            ]);

        } catch (FatalRequestException $e) {
            self::log('OneID: Logout transport error', 'N/A', $e->getMessage());

            return OneIDLogoutResult::from([
                'success' => false,
                'message' => 'Failed to log out (transport error)',
                'status' => null,
                'error' => $e->getMessage(),
            ]);

        } catch (RequestException $e) {
            $resp = $e->getResponse();
            $body = $resp?->body();

            self::log('OneID: Logout rejected', 'N/A', [
                'status' => $resp?->status(),
                'body' => $body,
            ]);

            return OneIDLogoutResult::from([
                'success' => false,
                'message' => 'Failed to log out',
                'status' => $resp?->status(),
                'error' => $body,
            ]);
        }
    }

    /**
     * Exchange OneID authorization code for access token.
     *
     * @param string $code The authorization code from OneID callback
     * @return string|null         Access token, or null on failure (already logged)
     */
    private static function oneIdGetToken(string $code): ?string
    {
        try {
            $response = self::send(new OneIDGetTokenRequest($code));
            return $response->json('access_token');

        } catch (FatalRequestException $e) {
            // Network/transport issue: DNS, SSL, timeout, base URL error, etc.
            self::log('OneID: Fatal transport error while requesting token', $code, $e->getMessage());
            return null;

        } catch (RequestException $e) {
            // Server returned 4xx/5xx (invalid_grant, invalid client, etc.)
            $resp = $e->getResponse();
            self::log('OneID: Token request rejected', $code, [
                'status' => $resp?->status(),
                'body' => $resp?->body(),
            ]);
            return null;

        } catch (JsonException $e) {
            // Malformed or unexpected JSON response
            self::log('OneID: Failed to parse token response', $code, $e->getMessage());
            return null;
        }
    }


    /**
     * Internal handler: code -> token -> handle/profile call.
     *
     * @return array{success:bool,message:string,status?:int,data?:array|null,error?:string|null}
     */
    private static function oneIdHandle(string $code): array
    {
        // 1) Get access token
        $accessToken = self::oneIdGetToken($code);

        if ($accessToken === null || $accessToken === '') {
            return [
                'success' => false,
                'message' => 'Could not obtain access token from OneID',
                'status' => null,
                'data' => null,
                'error' => 'token_null',
            ];
        }

        // 2) Call OneID handle/profile endpoint
        try {
            $response = self::send(new OneIDHandleRequest($accessToken));
            $json = $response->json();
            $key = config('oneid.user.pin_field', 'pin');

            // Customize validation: require 'pin' key in payload
            $hasIdentifier = is_array($json) && array_key_exists($key, $json);

            if (!$hasIdentifier) {
                self::log("OneID: Handle payload missing '$key'", $code, $response->body());

                return [
                    'success' => false,
                    'message' => "OneID handle response is missing required '$key'",
                    'status' => $response->status(),
                    'data' => $json ?? null,
                    'error' => 'missing_key',
                ];
            }

            return [
                'success' => true,
                'message' => 'Authorized successfully',
                'status' => $response->status(),
                'data' => $json,
                'error' => null,
            ];

        } catch (FatalRequestException $e) {
            self::log('OneID: Handle transport error', $code, $e->getMessage());

            return [
                'success' => false,
                'message' => 'Transport error while calling OneID handle endpoint',
                'status' => null,
                'data' => null,
                'error' => $e->getMessage(),
            ];

        } catch (RequestException $e) {
            $resp = $e->getResponse();
            $body = $resp?->body();

            self::log('OneID: Handle request rejected', $code, [
                'status' => $resp?->status(),
                'body' => $body,
            ]);

            return [
                'success' => false,
                'message' => 'OneID handle request failed',
                'status' => $resp?->status(),
                'data' => null,
                'error' => $body,
            ];

        } catch (JsonException $e) {
            self::log('OneID: Failed to parse handle response', $code, $e->getMessage());

            return [
                'success' => false,
                'message' => 'Failed to parse OneID handle response',
                'status' => null,
                'data' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Resolve connector via container.
     */
    private static function getConnector(): OneIDConnector
    {
        return app(OneIDConnector::class);
    }

    /**
     * Send request and throw on 4xx/5xx to unify error handling.
     *
     * @throws FatalRequestException
     * @throws RequestException
     */
    private static function send(Request $request): Response
    {
        return self::getConnector()->send($request);
    }

    /**
     * Generate OneID authorization URL
     */
    public static function getAuthorizationUrl(): string
    {
        $baseUrl = config('oneid.base_url');
        $endpoint = config('oneid.endpoints.authorization', '/sso/oauth/Authorization.do');
        $clientId = config('oneid.client_id');
        $redirectUri = config('oneid.redirect_uri');
        $scope = config('oneid.scope', 'openid profile');

        $params = http_build_query([
            'response_type' => 'one_code', // Rasmiy hujjatga ko'ra 'one_code' bo'lishi kerak
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'scope' => $scope,
            'state' => csrf_token(), // Add CSRF protection
        ]);

        return $baseUrl . $endpoint . '?' . $params;
    }

    /**
     * Get user information using access token
     */
    public static function getUserInfo(string $accessToken): array
    {
        try {
            $response = self::send(new OneIDHandleRequest($accessToken));
            return $response->json();

        } catch (FatalRequestException $e) {
            self::log('OneID: Get user info transport error', 'N/A', $e->getMessage());
            throw $e;

        } catch (RequestException $e) {
            $resp = $e->getResponse();
            self::log('OneID: Get user info request rejected', 'N/A', [
                'status' => $resp?->status(),
                'body' => $resp?->body(),
            ]);
            throw $e;
        }
    }

    /**
     * Validate access token
     */
    public static function validateToken(string $accessToken): bool
    {
        try {
            $userInfo = self::getUserInfo($accessToken);
            $pinField = config('oneid.user.pin_field', 'pin');
            
            return is_array($userInfo) && array_key_exists($pinField, $userInfo);

        } catch (\Exception $e) {
            self::log('OneID: Token validation failed', 'N/A', $e->getMessage());
            return false;
        }
    }

    /**
     * Get access token from authorization code
     */
    public static function getAccessToken(string $code): ?string
    {
        return self::oneIdGetToken($code);
    }

    /**
     * Check if OneID is properly configured
     */
    public static function isConfigured(): bool
    {
        $required = ['client_id', 'client_secret', 'redirect_uri', 'base_url'];
        
        foreach ($required as $key) {
            if (empty(config("oneid.{$key}"))) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get OneID configuration validation errors
     */
    public static function getConfigurationErrors(): array
    {
        $errors = [];
        $required = [
            'client_id' => 'OneID Client ID',
            'client_secret' => 'OneID Client Secret', 
            'redirect_uri' => 'OneID Redirect URI',
            'base_url' => 'OneID Base URL',
        ];

        foreach ($required as $key => $label) {
            if (empty(config("oneid.{$key}"))) {
                $errors[] = "{$label} is required but not configured";
            }
        }

        return $errors;
    }

    /**
     * Structured error logging.
     */
    private static function log(string $message, string $code, mixed $error): void
    {
        if (!config('oneid.logging.enabled', true)) {
            return;
        }

        $level = config('oneid.logging.level', 'info');
        $channel = config('oneid.logging.channel', 'default');

        Log::channel($channel)->{$level}($message, [
            'code' => $code,
            'datetime' => now()->toDateTimeString(),
            'error' => $error,
            'package' => 'oneid',
        ]);
    }
}

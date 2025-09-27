<?php

namespace Aslnbxrz\OneId;

use Aslnbxrz\OneId\Data\OneIDAuthResult;
use Aslnbxrz\OneId\Data\OneIDLogoutResult;

/**
 * OneID Manager - Main interface for OneID operations
 * 
 * This class provides a clean interface for all OneID operations
 * and serves as the accessor for the OneID facade.
 */
class OneIDManager
{
    /**
     * Handle OneID authentication callback
     * Exchange authorization code for user profile data
     */
    public function handle(string $code): OneIDAuthResult
    {
        return OneIDService::handle($code);
    }

    /**
     * Logout user from OneID system
     */
    public function logout(string $accessToken): OneIDLogoutResult
    {
        return OneIDService::logout($accessToken);
    }

    /**
     * Generate OneID authorization URL
     */
    public function getAuthorizationUrl(): string
    {
        return OneIDService::getAuthorizationUrl();
    }

    /**
     * Get user information using access token
     */
    public function getUserInfo(string $accessToken): array
    {
        return OneIDService::getUserInfo($accessToken);
    }

    /**
     * Validate access token
     */
    public function validateToken(string $accessToken): bool
    {
        return OneIDService::validateToken($accessToken);
    }

    /**
     * Get configuration value or entire config
     */
    public function getConfig(?string $key = null): mixed
    {
        if ($key === null) {
            return config('oneid');
        }

        return config("oneid.{$key}");
    }

    /**
     * Check if OneID routes are enabled
     */
    public function routesEnabled(): bool
    {
        return config('oneid.routes.enabled', true);
    }

    /**
     * Get OneID route names
     */
    public function getRouteNames(): array
    {
        return config('oneid.routes.names', []);
    }

    /**
     * Get OneID base URL
     */
    public function getBaseUrl(): string
    {
        return config('oneid.base_url');
    }

    /**
     * Check if OneID is properly configured
     */
    public function isConfigured(): bool
    {
        return OneIDService::isConfigured();
    }

    /**
     * Get OneID configuration validation errors
     */
    public function getConfigurationErrors(): array
    {
        return OneIDService::getConfigurationErrors();
    }
}
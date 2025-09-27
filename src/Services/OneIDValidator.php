<?php

namespace Aslnbxrz\OneId\Services;

use Illuminate\Support\Facades\Validator;

/**
 * OneID Validation Service
 *
 * Provides validation methods for OneID operations
 */
class OneIDValidator
{
    /**
     * Validate OneID configuration
     */
    public static function validateConfiguration(): array
    {
        $errors = [];

        $required = [
            'client_id' => 'OneID Client ID is required',
            'client_secret' => 'OneID Client Secret is required',
            'redirect_uri' => 'OneID Redirect URI is required',
            'base_url' => 'OneID Base URL is required',
        ];

        foreach ($required as $key => $message) {
            if (empty(config("oneid.{$key}"))) {
                $errors[] = $message;
            }
        }

        // Validate redirect URI format
        $redirectUri = config('oneid.redirect_uri');
        if ($redirectUri && ! filter_var($redirectUri, FILTER_VALIDATE_URL)) {
            $errors[] = 'OneID Redirect URI must be a valid URL';
        }

        // Validate base URL format
        $baseUrl = config('oneid.base_url');
        if ($baseUrl && ! filter_var($baseUrl, FILTER_VALIDATE_URL)) {
            $errors[] = 'OneID Base URL must be a valid URL';
        }

        return $errors;
    }

    /**
     * Validate authorization code
     */
    public static function validateAuthorizationCode(string $code): array
    {
        $validator = Validator::make(['code' => $code], [
            'code' => 'required|string|min:10|max:500',
        ]);

        return $validator->errors()->all();
    }

    /**
     * Validate access token
     */
    public static function validateAccessToken(string $accessToken): array
    {
        $validator = Validator::make(['access_token' => $accessToken], [
            'access_token' => 'required|string|min:10|max:1000',
        ]);

        return $validator->errors()->all();
    }

    /**
     * Validate user data from OneID response
     */
    public static function validateUserData(array $userData): array
    {
        $errors = [];
        $requiredFields = config('oneid.user.required_fields', ['pin', 'first_name', 'last_name']);

        foreach ($requiredFields as $field) {
            if (! array_key_exists($field, $userData) || empty($userData[$field])) {
                $errors[] = "Required field '{$field}' is missing or empty";
            }
        }

        // Validate PIN format (should be numeric and 14 digits)
        if (isset($userData['pin']) && ! preg_match('/^\d{14}$/', $userData['pin'])) {
            $errors[] = 'PIN must be exactly 14 digits';
        }

        // Validate user_type
        if (isset($userData['user_type']) && ! in_array($userData['user_type'], ['I', 'L'])) {
            $errors[] = 'User type must be either I (Individual) or L (Legal entity)';
        }

        // Validate auth_method
        if (isset($userData['auth_method'])) {
            $validAuthMethods = ['LOGINPASSMETHOD', 'MOBILEMETHOD', 'PKCSMETHOD', 'LEPKCSMETHOD', 'QR'];
            if (! in_array($userData['auth_method'], $validAuthMethods)) {
                $errors[] = 'Invalid authentication method';
            }
        }

        // Validate ret_cd
        if (isset($userData['ret_cd']) && ! in_array($userData['ret_cd'], ['0', '1'])) {
            $errors[] = 'Return code must be 0 (success) or 1 (failure)';
        }

        // Validate birth_date format (YYYYMMDD or YYYY-MM-DD)
        if (isset($userData['birth_date']) && ! empty($userData['birth_date'])) {
            if (! preg_match('/^\d{8}$/', $userData['birth_date']) &&
                ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $userData['birth_date'])) {
                $errors[] = 'Birth date must be in YYYYMMDD or YYYY-MM-DD format';
            }
        }

        // Validate legal_info structure if present
        if (isset($userData['legal_info']) && is_array($userData['legal_info'])) {
            foreach ($userData['legal_info'] as $index => $legalEntity) {
                if (! isset($legalEntity['tin']) || ! preg_match('/^\d{9}$/', $legalEntity['tin'])) {
                    $errors[] = "Legal entity #{$index}: TIN must be exactly 9 digits";
                }

                if (! isset($legalEntity['le_name']) || empty($legalEntity['le_name'])) {
                    $errors[] = "Legal entity #{$index}: Legal entity name is required";
                }
            }
        }

        return $errors;
    }

    /**
     * Check if OneID is properly configured
     */
    public static function isConfigured(): bool
    {
        return empty(self::validateConfiguration());
    }
}

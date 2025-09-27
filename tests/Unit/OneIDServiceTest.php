<?php

namespace Aslnbxrz\OneId\Tests\Unit;

use Aslnbxrz\OneId\OneIDService;
use Aslnbxrz\OneId\OneIdServiceProvider;
use Aslnbxrz\OneId\Services\OneIDValidator;
use Orchestra\Testbench\TestCase;

class OneIDServiceTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            OneIdServiceProvider::class,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Set up test configuration
        config([
            'oneid.base_url' => 'https://sso.egov.uz',
            'oneid.client_id' => 'test_client_id',
            'oneid.client_secret' => 'test_client_secret',
            'oneid.redirect_uri' => 'https://example.com/callback',
            'oneid.scope' => 'openid profile',
        ]);
    }

    public function test_it_can_check_if_oneid_is_configured()
    {
        $this->assertTrue(OneIDService::isConfigured());
    }

    public function test_it_returns_false_when_missing_required_config()
    {
        config(['oneid.client_id' => null]);

        $this->assertFalse(OneIDService::isConfigured());
    }

    public function test_it_can_get_configuration_errors()
    {
        config([
            'oneid.client_id' => null,
            'oneid.client_secret' => null,
        ]);

        $errors = OneIDService::getConfigurationErrors();

        $this->assertCount(2, $errors);
        $this->assertContains('OneID Client ID is required but not configured', $errors);
        $this->assertContains('OneID Client Secret is required but not configured', $errors);
    }

    public function test_it_can_generate_authorization_url()
    {
        $url = OneIDService::getAuthorizationUrl();

        $this->assertStringContainsString('https://sso.egov.uz', $url);
        $this->assertStringContainsString('response_type=one_code', $url);
        $this->assertStringContainsString('client_id=test_client_id', $url);
        $this->assertStringContainsString('redirect_uri='.urlencode('https://example.com/callback'), $url);
        $this->assertStringContainsString('scope='.urlencode('openid profile'), $url);
    }

    public function test_it_can_validate_authorization_code()
    {
        $validCode = 'valid_authorization_code_123';
        $errors = OneIDValidator::validateAuthorizationCode($validCode);

        $this->assertEmpty($errors);
    }

    public function test_it_rejects_invalid_authorization_code()
    {
        $invalidCode = 'short';
        $errors = OneIDValidator::validateAuthorizationCode($invalidCode);

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('must be at least', $errors[0]);
    }

    public function test_it_can_validate_access_token()
    {
        $validToken = 'valid_access_token_123456789';
        $errors = OneIDValidator::validateAccessToken($validToken);

        $this->assertEmpty($errors);
    }

    public function test_it_rejects_invalid_access_token()
    {
        $invalidToken = 'short';
        $errors = OneIDValidator::validateAccessToken($invalidToken);

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('must be at least', $errors[0]);
    }

    public function test_it_can_validate_user_data()
    {
        $userData = [
            'pin' => '12345678901234',
            'first_name' => 'John',
            'sur_name' => 'Doe', // Rasmiy hujjatga ko'ra 'sur_name'
            'mid_name' => 'Smith', // Rasmiy hujjatga ko'ra 'mid_name'
            'user_type' => 'I',
            'ret_cd' => '0',
        ];

        $errors = OneIDValidator::validateUserData($userData);

        $this->assertEmpty($errors);
    }

    public function test_it_rejects_user_data_with_invalid_pin()
    {
        $userData = [
            'pin' => '123', // Too short
            'first_name' => 'John',
            'last_name' => 'Doe',
        ];

        $errors = OneIDValidator::validateUserData($userData);

        $this->assertNotEmpty($errors);
        $this->assertContains('PIN must be exactly 14 digits', $errors);
    }

    public function test_it_rejects_user_data_with_missing_required_fields()
    {
        $userData = [
            'pin' => '12345678901234',
            // Missing first_name and last_name
        ];

        $errors = OneIDValidator::validateUserData($userData);

        $this->assertNotEmpty($errors);
        $this->assertContains("Required field 'first_name' is missing or empty", $errors);
        $this->assertContains("Required field 'sur_name' is missing or empty", $errors);
    }
}

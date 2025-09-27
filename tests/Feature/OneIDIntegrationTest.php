<?php

namespace Aslnbxrz\OneId\Tests\Feature;

use Aslnbxrz\OneId\Facades\OneID;
use Aslnbxrz\OneId\OneIdServiceProvider;
use Aslnbxrz\OneId\Services\OneIDValidator;
use Orchestra\Testbench\TestCase;

class OneIDIntegrationTest extends TestCase
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

    public function test_it_can_validate_configuration()
    {
        $errors = OneIDValidator::validateConfiguration();

        $this->assertEmpty($errors, 'Configuration should be valid for testing');
    }

    public function test_it_can_generate_authorization_url()
    {
        $url = OneID::getAuthorizationUrl();

        $this->assertStringContainsString('https://sso.egov.uz', $url);
        $this->assertStringContainsString('response_type=one_code', $url);
        $this->assertStringContainsString('client_id=test_client_id', $url);
    }

    public function test_it_can_check_if_configured()
    {
        $this->assertTrue(OneID::isConfigured());
    }

    public function test_it_can_get_configuration_errors()
    {
        config(['oneid.client_id' => null]);

        $errors = OneID::getConfigurationErrors();

        $this->assertNotEmpty($errors);
        $this->assertContains('OneID Client ID is required but not configured', $errors);
    }

    public function test_it_can_get_config_value()
    {
        $baseUrl = OneID::getConfig('base_url');
        $this->assertEquals('https://sso.egov.uz', $baseUrl);

        $fullConfig = OneID::getConfig();
        $this->assertIsArray($fullConfig);
        $this->assertArrayHasKey('base_url', $fullConfig);
    }

    public function test_it_can_check_routes_enabled()
    {
        $enabled = OneID::routesEnabled();
        $this->assertTrue($enabled);
    }

    public function test_it_can_get_route_names()
    {
        $routeNames = OneID::getRouteNames();

        $this->assertIsArray($routeNames);
        $this->assertArrayHasKey('handle', $routeNames);
        $this->assertArrayHasKey('logout', $routeNames);
        $this->assertArrayHasKey('redirect', $routeNames);
    }
}

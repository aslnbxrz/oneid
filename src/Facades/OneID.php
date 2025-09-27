<?php

namespace Aslnbxrz\OneId\Facades;

use Aslnbxrz\OneId\Data\OneIDAuthResult;
use Aslnbxrz\OneId\Data\OneIDLogoutResult;
use Illuminate\Support\Facades\Facade;

/**
 * @method static OneIDAuthResult handle(string $code)
 * @method static OneIDLogoutResult logout(string $accessToken)
 * @method static string getAuthorizationUrl()
 * @method static array getUserInfo(string $accessToken)
 * @method static bool validateToken(string $accessToken)
 * @method static array getConfig(string $key = null)
 * @method static bool isConfigured()
 * @method static array getConfigurationErrors()
 * @method static bool routesEnabled()
 * @method static array getRouteNames()
 * @method static string getBaseUrl()
 *
 * @see \Aslnbxrz\OneId\OneIDManager
 */
class OneID extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        // This must match the container binding key in the ServiceProvider.
        return 'oneid';
    }
}

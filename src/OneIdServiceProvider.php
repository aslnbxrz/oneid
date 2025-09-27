<?php

namespace Aslnbxrz\OneId;

use Aslnbxrz\OneId\Commands\ValidateOneIDConfigCommand;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class OneIdServiceProvider extends PackageServiceProvider
{
    public static string $vendor = 'aslnbxrz';

    public static string $name = 'oneid';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(self::$name)
            ->hasConfigFile('oneid')
            ->hasCommand(ValidateOneIDConfigCommand::class)
            ->hasInstallCommand(fn (InstallCommand $command) => $command
                ->publishConfigFile()
                ->copyAndRegisterServiceProviderInApp()
                ->askToStarRepoOnGitHub(self::$vendor.'/'.self::$name)
            );

        // Conditionally load routes based on configuration
        if (config('oneid.routes.enabled', true)) {
            $package->hasRoute('oneid');
        }
    }

    public function registeringPackage(): void
    {
        // Bind manager as a singleton for the facade accessor 'oneid'
        $this->app->singleton('oneid', OneIDManager::class);
    }
}

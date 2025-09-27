<?php

namespace Aslnbxrz\OneId\Commands;

use Aslnbxrz\OneId\Services\OneIDValidator;
use Illuminate\Console\Command;

class ValidateOneIDConfigCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'oneid:validate-config';

    /**
     * The console command description.
     */
    protected $description = 'Validate OneID configuration';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Validating OneID configuration...');
        $this->newLine();

        $errors = OneIDValidator::validateConfiguration();

        if (empty($errors)) {
            $this->info('✅ OneID configuration is valid!');
            $this->newLine();

            $this->table(['Configuration', 'Value'], [
                ['Base URL', config('oneid.base_url')],
                ['Client ID', config('oneid.client_id')],
                ['Client Secret', str_repeat('*', strlen(config('oneid.client_secret')))],
                ['Scope', config('oneid.scope')],
                ['Redirect URI', config('oneid.redirect_uri')],
                ['Routes Enabled', config('oneid.routes.enabled') ? 'Yes' : 'No'],
                ['Logging Enabled', config('oneid.logging.enabled') ? 'Yes' : 'No'],
            ]);

            return self::SUCCESS;
        }

        $this->error('❌ OneID configuration has errors:');
        $this->newLine();

        foreach ($errors as $error) {
            $this->line("  • {$error}");
        }

        $this->newLine();
        $this->info('Please check your .env file and config/oneid.php configuration.');

        return self::FAILURE;
    }
}

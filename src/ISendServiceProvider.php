<?php

namespace ISend\SMS;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as LumenApplication;
use ISend\SMS\Console\Commands\ISendSetupCommand;

class ISendServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/isend.php', 'isend'
        );

        $this->app->singleton(ISend::class, function ($app) {
            $config = $app['config']['isend'];
            
            // Check for missing required configuration
            if (empty($config['api_token'])) {
                throw new \InvalidArgumentException(
                    'The ISend SMS service requires an API token. Please set the ISEND_API_TOKEN environment variable.'
                );
            }
            
            return new ISend(
                $config['api_token'],
                $config['base_url'],
                $config['api_version_path'],
                $config['default_sender_id']
            );
        });
        
        // Register alias for backwards compatibility
        $this->app->alias(ISend::class, 'isend');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->setupConfig();
        
        // Register commands if running in console
        if ($this->app->runningInConsole()) {
            $this->commands([
                ISendSetupCommand::class,
            ]);
        }
    }
    
    /**
     * Setup the config.
     */
    protected function setupConfig(): void
    {
        $source = __DIR__.'/../config/isend.php';
        
        if ($this->app instanceof LaravelApplication) {
            // Laravel
            $this->publishes([
                $source => config_path('isend.php'),
            ], 'isend-config');
        } elseif ($this->app instanceof LumenApplication) {
            // Lumen
            $this->app->configure('isend');
        }
    }
    
    /**
     * Get the services provided by the provider.
     *
     * @return array<int, string>
     */
    public function provides(): array
    {
        return [ISend::class, 'isend', ISendSetupCommand::class];
    }
}
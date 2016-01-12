<?php

namespace Sterling\Responses;

use Illuminate\Support\ServiceProvider;

class SterlingResponseServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../../lang', 'responses');

        $this->publishes([
            __DIR__ . '/../../lang'           => base_path('resources/lang/vendor/sterling-responses')
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [ ];
    }
}
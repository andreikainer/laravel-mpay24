<?php

namespace LaravelMPay24;

use Illuminate\Support\ServiceProvider;

class LaravelMPay24ServiceProvider extends ServiceProvider {
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    private $merchantId;
    private $password;
    private $testMode = false;
    private $debugMode = false;

    public function __construct($app)
    {
        $this->merchantId = config('services.mpay24.merchantId');
        $this->password = config('services.mpay24.password');
        $this->testMode = config('services.mpay24.test', false);
        $this->debugMode = config('services.mpay24.debug', false);

        parent::__construct($app);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('mpay24', function()
        {
            return new Shop($this->merchantId, $this->password, $this->testMode, null, null, $this->debugMode);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['mpay24'];
    }
}
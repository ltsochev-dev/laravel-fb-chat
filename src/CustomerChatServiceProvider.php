<?php

namespace Ltsochev\CustomerChat;

use Illuminate\Support\ServiceProvider;

class CustomerChatServiceProvider extends ServiceProvider
{
    /**
     * Register application configuration
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'ltsochev-customerchat');

        $this->publishes([
            __DIR__ . '/../config/config.php' => config_path('customerchat.php')
        ]);
    }

    /**
     * Register application services
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/config.php', 'customerchat'
        );

        $this->app->bind('customer-chat', function($app) {
            $config = $app['config']['customerchat'];

            return new CustomerChat($app['view'], $config);
        });


        $this->registerMiddleware();
    }

    /**
     * Registers the auto inject middleware
     *
     * @return void
     */
    private function registerMiddleware()
    {
        if ($this->app['config']['customerchat']['enabled'] &&
            $this->app['config']['customerchat']['autoinject']) {
                $router = $this->app['router'];
                $router->pushMiddlewareToGroup('web', \Ltsochev\CustomerChat\Middleware\AutoInjectMiddleware::class);
            }
    }
}

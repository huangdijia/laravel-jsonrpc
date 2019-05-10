<?php

namespace Huangdijia\JsonRpc;

use Huangdijia\JsonRpc\Client;
use Illuminate\Support\ServiceProvider;

class JsonRpcServiceProvider extends ServiceProvider
{
    protected $defer = true;

    public function boot()
    {
        //
    }

    public function register()
    {
        $this->app->bind(Client::class, function ($app, $parameters) {
            static $instances = [];

            $key = $parameters['url'];

            if (!isset($instances[$key])) {
                $instances[$key] = new Client($parameters['url'], $parameters['debug'] ?? false);
            }

            return $instances[$key];
        });
        $this->app->alias(Client::class, 'jsonrpc.client');
    }

    public function provides()
    {
        return [];
    }
}

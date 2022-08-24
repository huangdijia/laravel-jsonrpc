<?php
/**
 * This file is part of laravel-jsonrpc.
 *
 * @link https://github.com/huangdijia/laravel-jsonrpc/blob/2.x/README.md
 * @contact  huangdijia@gmail.com
 * @license  https://github.com/huangdijia/laravel-jsonrpc/blob/2.x/LICENSE
 */
namespace Huangdijia\JsonRpc;

use Illuminate\Support\ServiceProvider;

class JsonRpcServiceProvider extends ServiceProvider
{
    protected $defer = true;

    public function boot()
    {
    }

    public function register()
    {
    }

    public function provides()
    {
        return [
        ];
    }
}

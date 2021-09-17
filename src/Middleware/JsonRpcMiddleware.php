<?php
/**
 * This file is part of laravel-jsonrpc.
 *
 * @link     https://github.com
 * @document https://github.com/huangdijia/laravel-jsonrpc/blob/2.x/README.md
 * @contact  hdj@addcn.com
 * @license  https://github.com/huangdijia/laravel-jsonrpc/blob/2.x/LICENSE
 */
namespace Huangdijia\JsonRpc\Middleware;

use Closure;
use Huangdijia\JsonRpc\Packer;
use Throwable;

class JsonRpcMiddleware
{
    /**
     * @var Packer
     */
    private $packer;

    public function __construct(Packer $packer)
    {
        $this->packer = $packer;
    }

    /**
     * Handle an incoming request.
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            return $next($request);
        } catch (Throwable $e) {
            return response()->json($this->packer->pack(null, $e->getMessage()));
        }
    }
}

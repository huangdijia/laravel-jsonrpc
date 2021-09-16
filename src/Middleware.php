<?php
/**
 * This file is part of laravel-jsonrpc.
 *
 * @link     https://github.com
 * @document https://github.com/huangdijia/laravel-jsonrpc/blob/2.x/README.md
 * @contact  hdj@addcn.com
 * @license  https://github.com/huangdijia/laravel-jsonrpc/blob/2.x/LICENSE
 */
namespace Huangdijia\JsonRpc;

use Closure;
use Throwable;

class Middleware
{
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
            return response()->json([
                'jsonrpc' => '2.0',
                'result' => [],
                'error' => $e->getMessage(),
                'id' => $request['id'] ?? 1,
            ]);
        }
    }
}

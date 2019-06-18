<?php

namespace Huangdijia\JsonRpc;

use Closure;
use Exception;

class Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            return $next($request);
        } catch (Exception $e) {
            return response()->json([
                'jsonrpc' => '2.0',
                'result'  => [],
                'error'   => $e->getMessage(),
                'id'      => $request['id'] ?? 1,
            ]);
        }
    }
}

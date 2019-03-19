<?php

namespace Huangdijia\JsonRpc;

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
        $error = '';

        try {
            $response = $next($request);
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        return response()->json([
            'jsonrpc' => '2.0',
            'result'  => $response->getOriginalContent(),
            'error'   => $error,
            'id'      => $request['id'] ?? 1,
        ]);
    }
}
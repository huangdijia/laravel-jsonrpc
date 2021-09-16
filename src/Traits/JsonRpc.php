<?php
/**
 * This file is part of laravel-jsonrpc.
 *
 * @link     https://github.com
 * @document https://github.com/huangdijia/laravel-jsonrpc/blob/2.x/README.md
 * @contact  hdj@addcn.com
 * @license  https://github.com/huangdijia/laravel-jsonrpc/blob/2.x/LICENSE
 */
namespace Huangdijia\JsonRpc\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

trait JsonRpc
{
    /**
     * Invoke.
     * @return array
     */
    final public function __invoke(Request $request)
    {
        $id = (int) $request->input('id', 1);
        $method = (string) $request->input('method', 'index');
        $params = (array) $request->input('params', []);

        if (! $request->isMethod('POST')) {
            return $this->pack(null, "Request method must be POST, {$request->method()} given.", $id);
        }

        if (! is_callable([$this, $method])) {
            return $this->pack(null, sprintf("class '%s' does not have a method '%s'", get_class($this), $method), $id);
        }

        try {
            $result = call_user_func_array([$this, $method], $params);

            // Get data from JsonResponse
            if ($result instanceof JsonResponse) {
                $result = $result->getData();
            }

            // Has packed
            if (is_array($result) && isset($result['jsonrpc'])) {
                return $result;
            }

            // Return as array
            return $this->pack($result, null, $id);
        } catch (Throwable $e) {
            return $this->pack(null, $e->getMessage(), $id);
        }
    }

    /**
     * Pack.
     * @param mixed $result
     * @return array
     */
    protected function pack($result, ?string $error = null, ?int $id = 1)
    {
        return [
            'jsonrpc' => 2.0,
            'result' => $result,
            'error' => $error,
            'id' => $id,
        ];
    }
}

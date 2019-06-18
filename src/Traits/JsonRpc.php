<?php

namespace Huangdijia\JsonRpc\Traits;

use Exception;
use Illuminate\Http\Request;

trait JsonRpc
{
    /**
     * Invoke
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    final public function __invoke(Request $request)
    {
        $id     = $request->input('id', 1);
        $method = $request->input('method', 'index');
        $params = $request->input('params', []);

        if (!is_callable($this, $method)) {
            throw new Exception("Method '{$method}' is not callable!", 1);
        }

        return call_user_func_array([$this, $method], $params);
    }

    /**
     * Failure
     *
     * @param string $error
     * @param mixed $result
     * @return array
     */
    public function failure(strin $error, $result = '')
    {
        return [
            'jsonrpc' => config('jsonrpc.version', '2.0'),
            'result'  => $result,
            'error'   => $error,
            'id'      => app('request')->input('id', 1),
        ];
    }

    /**
     * Success
     *
     * @param array $result
     * @return array
     */
    public function success($result = [])
    {
        return [
            'jsonrpc' => config('jsonrpc.version', '2.0'),
            'result'  => $result,
            'error'   => null,
            'id'      => app('request')->input('id', 1),
        ];
    }
}

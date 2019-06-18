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

        if (!is_callable([$this, $method])) {
            return $this->failure(sprintf("class '%s' does not have a method '%s'", get_class($this), $method));
        }

        try {
            $result = call_user_func_array([$this, $method], $params);

            if (!isset($result['jsonrpc'])) {
                $result = $this->response($result);
            }

            return $result;
        } catch (Exception $e) {
            return $this->failure($e->getMessage());
        }
    }

    /**
     * Failure
     *
     * @param string $error
     * @param mixed $result
     * @return array
     */
    public function failure(string $error, $result = '')
    {
        return $this->response($result, $error);
    }

    /**
     * Response
     *
     * @param mixed $result
     * @return array
     */
    public function response($result = [], string $error = null)
    {
        return [
            'jsonrpc' => config('jsonrpc.version', '2.0'),
            'result'  => $result,
            'error'   => $error,
            'id'      => app('request')->input('id', 1),
        ];
    }
}

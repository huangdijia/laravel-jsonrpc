<?php

namespace Huangdijia\JsonRpc\Traits;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

trait JsonRpc
{
    /**
     * Invoke
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    final public function __invoke(Request $request)
    {
        $id     = (int) $request->input('id', 1);
        $method = (string) $request->input('method', 'index');
        $params = (array) $request->input('params', []);

        if (!is_callable([$this, $method])) {
            return $this->packaging([], sprintf("class '%s' does not have a method '%s'", get_class($this), $method), $id);
        }

        try {
            $result = call_user_func_array([$this, $method], $params);

            // get data from JsonResponse
            if ($result instanceof JsonResponse) {
                $result = $result->getData();
            }

            // has packed
            if (is_array($result) && isset($result['jsonrpc'])) {
                return $result;
            }

            // return array
            return $this->packaging($result, null, $id);
        } catch (Exception $e) {
            return $this->packaging('', $e->getMessage(), $id);
        }
    }

    /**
     * Response
     *
     * @param mixed $result
     * @param string $error
     * @param int $id
     * @return array
     */
    public function packaging($result = [], string $error = null, int $id = 1)
    {
        return [
            'jsonrpc' => config('jsonrpc.version', '2.0'),
            'result'  => $result,
            'error'   => $error,
            'id'      => $id,
        ];
    }
}

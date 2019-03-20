<?php

namespace Huangdijia\JsonRpc;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

abstract class LumenController extends BaseController
{
    final public function __invoke(Request $request)
    {
        if (!is_subclass_of($this, LumenController::class)) {
            throw new \Exception(get_class($this) . " must instanceof " . \Huangdijia\JsonRpc\LumenController::class, 1);
        }

        $id     = $request->input('id', 1);
        $method = $request->input('method', 'index');
        $params = $request->input('params', []);

        if (!is_callable($this, $method)) {
            throw new \Exception("Method '{$method}' is not callable!", 1);
        }

        return call_user_func_array([$this, $method], $params);
    }
}

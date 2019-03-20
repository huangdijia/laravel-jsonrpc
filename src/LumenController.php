<?php

namespace Huangdijia\JsonRpc;

use Laravel\Lumen\Routing\Controller as BaseController;

abstract class LumenController extends BaseController
{
    final public function __invoke()
    {
        if (!($this instanceof \Huangdijia\JsonRpc\Controller)) {
            throw new \Exception(get_class($this) . " must instanceof " . \Huangdijia\JsonRpc\LumenController::class, 1);
        }

        $id     = request()->input('id', 1);
        $method = request()->input('method', 'index');
        $params = request()->input('params', []);

        if (!is_callable($this, $method)) {
            throw new \Exception("Method '{$method}' is not callable!", 1);
        }

        return call_user_func_array([$this, $method], $params);
    }
}

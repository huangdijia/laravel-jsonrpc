<?php

namespace Huangdijia\JsonRpc;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class JsonRpcController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    final public function __invoke()
    {
        $id     = request()->input('id', 1);
        $method = request()->input('method', 'index');
        $params = request()->input('params', []);

        if (!is_callable($this, $method)) {
            throw new \Exception("Method '{$method}' is not callable!", 1);
        }

        return call_user_func_array([$this, $method], $params);
    }
}

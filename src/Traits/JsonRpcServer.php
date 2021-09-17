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

use Exception;
use Huangdijia\JsonRpc\Packer;
use Illuminate\Http\Request;

trait JsonRpcServer
{
    /**
     * Invoke.
     * @return array
     */
    final public function __invoke(Request $request, Packer $packer)
    {
        $id = (int) $request->input('id', 1);
        $method = (string) $request->input('method', 'index');
        $params = (array) $request->input('params', []);

        if (! $request->isMethod('POST')) {
            throw new Exception(sprintf('Request method must be POST, %s given.', $request->method()));
        }

        if (! is_callable([$this, $method])) {
            throw new Exception(sprintf("Class '%s' does not have a method '%s'", get_class($this), $method));
        }

        $result = call_user_func_array([$this, $method], $params);

        // Return as array
        return $packer->pack($result, null, $id);
    }
}

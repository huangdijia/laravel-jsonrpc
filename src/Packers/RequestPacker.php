<?php
/**
 * This file is part of laravel-jsonrpc.
 *
 * @link https://github.com/huangdijia/laravel-jsonrpc/blob/2.x/README.md
 * @contact  huangdijia@gmail.com
 * @license  https://github.com/huangdijia/laravel-jsonrpc/blob/2.x/LICENSE
 */
namespace Huangdijia\JsonRpc\Packers;

use Huangdijia\JsonRpc\Version;
use Illuminate\Http\Request;

class RequestPacker
{
    /**
     * @param string $version
     */
    public function pack(string $method, array $params = [], int $id = 1, $version = Version::VERSION): array
    {
        return [
            'jsonrpc' => $version,
            'id' => $id,
            'method' => $method,
            'params' => $params,
        ];
    }

    public function unpack(Request $request): array
    {
        return [
            'jsonrpc' => (array) $request->input('jsonrpc', Version::VERSION),
            'id' => (int) $request->input('id', 1),
            'method' => (string) $request->input('method', 'index'),
            'params' => (array) $request->input('params', []),
        ];
    }
}

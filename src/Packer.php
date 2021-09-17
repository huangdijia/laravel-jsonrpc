<?php
/**
 * This file is part of laravel-jsonrpc.
 *
 * @link     https://github.com
 * @document https://github.com/huangdijia/laravel-jsonrpc/blob/2.x/README.md
 * @contact  hdj@addcn.com
 * @license  https://github.com/huangdijia/laravel-jsonrpc/blob/2.x/LICENSE
 */
namespace Huangdijia\JsonRpc;

class Packer
{
    public function pack($result, ?string $error = null, ?int $id = 1, $version = Version::VERSION): array
    {
        return [
            'jsonrpc' => $version,
            'result' => $result,
            'error' => $error,
            'id' => $id,
        ];
    }
}

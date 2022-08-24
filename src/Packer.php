<?php
/**
 * This file is part of laravel-jsonrpc.
 *
 * @link https://github.com/huangdijia/laravel-jsonrpc/blob/2.x/README.md
 * @contact  huangdijia@gmail.com
 * @license  https://github.com/huangdijia/laravel-jsonrpc/blob/2.x/LICENSE
 */
namespace Huangdijia\JsonRpc;

use Illuminate\Http\JsonResponse;
use Throwable;

class Packer
{
    /**
     * @param mixed $result
     * @param null|string|Throwable $error
     * @param string $version
     */
    public function pack($result, $error = null, ?int $id = null, $version = Version::VERSION): array
    {
        return [
            'jsonrpc' => $version,
            'id' => $id,
            'result' => $this->transformResult($result),
            'error' => $this->transformError($error),
        ];
    }

    protected function transformResult($result)
    {
        // Return when the result is a jsonrpc response
        if (isset($result['jsonrpc'])) {
            return $result;
        }

        // Get the result from json response
        if ($result instanceof JsonResponse) {
            return $result->getData();
        }

        return $result;
    }

    protected function transformError($error = null): ?array
    {
        if ($error instanceof Throwable) { // Get the error from throwable
            return [
                'message' => $error->getMessage(),
                'code' => $error->getCode(),
            ];
        }

        if (is_string($error)) { // Compatible with string error
            return [
                'message' => $error,
                'code' => 0,
            ];
        }

        return null;
    }
}

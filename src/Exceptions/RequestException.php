<?php
/**
 * This file is part of laravel-jsonrpc.
 *
 * @link     https://github.com
 * @document https://github.com/huangdijia/laravel-jsonrpc/blob/2.x/README.md
 * @contact  hdj@addcn.com
 * @license  https://github.com/huangdijia/laravel-jsonrpc/blob/2.x/LICENSE
 */
namespace Huangdijia\JsonRpc\Exceptions;

use Throwable;

class RequestException extends \Exception
{
    /**
     * @var null|string
     */
    private $url;

    public function __construct(string $message, ?string $url = null, int $code = 0, ?Throwable $previous = null)
    {
        $this->url = $url;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return null|string
     */
    public function getUrl()
    {
        return $this->url;
    }
}

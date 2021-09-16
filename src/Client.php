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

use Huangdijia\JsonRpc\Exceptions\RequestException;
use Illuminate\Http\Client\RequestException as HttpRequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class Client
{
    const JSONRPC_VERSION = '2.0';

    /**
     * @var string
     */
    private $url = '';

    /**
     * @var int
     */
    private $id = 0;

    /**
     * @var bool
     */
    private $notification = false;

    /**
     * @var array
     */
    private $headers = [];

    public function __construct(string $url)
    {
        $this->url = $url;
        $this->id = 1;
    }

    public function __call($method, $params)
    {
        if ($this->notification) {
            $currentId = null;
        } else {
            $currentId = $this->id;
        }

        $data = [
            'jsonrpc' => self::JSONRPC_VERSION,
            'method' => $method,
            'params' => $params,
            'id' => $currentId,
        ];

        /** @var array $response */
        $response = Http::withHeaders($this->headers)
            ->asJson()
            ->post($this->url, $data)
            ->throw(function (Response $response, HttpRequestException $exception) {
                throw new RequestException($exception->getMessage(), $this->url, $exception->getCode(), $exception);
            })
            ->json();

        if ($this->notification) {
            return true;
        }

        if (isset($response['id']) && $response['id'] != $currentId) {
            throw new RequestException(sprintf('Incorrect response id (request id: %s, response id: %s)', $currentId, $response['id']), $this->url);
        }

        if (isset($response['error']) && ! is_null($response['error'])) {
            throw new RequestException(printf('Request error: %s', $response['error']), $this->url);
        }

        if (! isset($response['result'])) {
            throw new RequestException('Error response[result]', $this->url);
        }

        return $response['result'];
    }

    public function setHeader(string $name, string $value)
    {
        if (in_array(strtolower($name), ['content-type', 'content-length'])) {
            return $this;
        }

        $this->headers[$name] = $value;

        return $this;
    }

    public function setRPCNotification(bool $notification)
    {
        $this->notification = $notification;

        return $this;
    }
}

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

    public function __construct(string $url, array $headers = [], bool $notification = false)
    {
        $this->url = $url;
        $this->headers = $headers;
        $this->notification = $notification;
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

        /** @var array $respond */
        $respond = Http::withHeaders($this->headers)
            ->asJson()
            ->post($this->url, $data)
            ->throw(function (Response $response, HttpRequestException $exception) {
                throw new RequestException($exception->getMessage(), $this->url, $exception->getCode(), $exception);
            })
            ->json();

        if ($this->notification) {
            return true;
        }

        if (isset($respond['id']) && $respond['id'] != $currentId) {
            throw new RequestException(sprintf('Incorrect response id (request id: %s, response id: %s)', $currentId, $respond['id']), $this->url);
        }

        if (isset($respond['error']) && ! is_null($respond['error'])) {
            throw new RequestException(printf('Request error: %s', $respond['error']), $this->url);
        }

        if (! isset($respond['result'])) {
            throw new RequestException('Error response[result]', $this->url);
        }

        return $respond['result'];
    }

    /**
     * @deprecated v3.x
     * @param array|string $name
     * @return $this
     */
    public function setHeader($name, ?string $value = null, bool $override = false)
    {
        if (is_array($name)) {
            foreach ($name as $key => $value) {
                $this->setHeader($key, $value, $override);
            }

            return $this;
        }

        if (! isset($this->headers[$name]) || $override) {
            $this->headers[$name] = $value;
        }

        return $this;
    }

    /**
     * @deprecated v3.x
     * @return $this
     */
    public function setRPCNotification(bool $notification)
    {
        $this->notification = $notification;

        return $this;
    }
}

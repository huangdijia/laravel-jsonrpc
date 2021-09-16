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
     * @var string 請求地址
     */
    private $url = '';

    /**
     * @var int 請求編號
     */
    private $id = 0;

    /**
     * @var bool 通知開關
     */
    private $notification = false;

    /**
     * @var array 请求头部
     */
    private $headers = [];

    /**
     * @param string $url 請示地址
     * @param bool $debug 測試開關
     */
    public function __construct($url, $debug = false)
    {
        // Server URL
        $this->url = $url;
        // Request ID
        $this->id = 1;
    }

    /**
     * @param string $method 請示方法
     * @param array $params 請求參數
     * @return mixed
     */
    public function __call($method, $params)
    {
        // 设置通知
        if ($this->notification) {
            $currentId = null;
        } else {
            $currentId = $this->id;
        }

        // 封闭参数
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

        // Return when just notification
        if ($this->notification) {
            return true;
        }

        // Check for request ID
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

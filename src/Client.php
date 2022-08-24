<?php
/**
 * This file is part of laravel-jsonrpc.
 *
 * @link https://github.com/huangdijia/laravel-jsonrpc/blob/2.x/README.md
 * @contact  huangdijia@gmail.com
 * @license  https://github.com/huangdijia/laravel-jsonrpc/blob/2.x/LICENSE
 */
namespace Huangdijia\JsonRpc;

use Huangdijia\JsonRpc\Exceptions\RequestException;
use Illuminate\Http\Client\RequestException as HttpRequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class Client
{
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
    private $options = [];

    public function __construct(string $url, array $headers = [], bool $notification = false)
    {
        $this->url = $url;
        $this->options['headers'] = $headers;
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
            'jsonrpc' => Version::VERSION,
            'method' => $method,
            'params' => $params,
            'id' => $currentId,
        ];

        /** @var array $respond */
        $respond = Http::withOptions($this->options)
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
            if (is_string($respond['error'])) { // Compatible with old version
                $respond['error'] = [
                    'message' => $respond['error'],
                    'code' => 0,
                ];
            }

            $message = $respond['error']['message'] ?? 'Unknown error';
            $code = (int) $respond['error']['code'] ?? 0;

            throw new RequestException($message, $this->url, $code);
        }

        if (! isset($respond['result'])) {
            throw new RequestException('Error response[result]', $this->url);
        }

        return $respond['result'];
    }

    /**
     * @return $this
     */
    public function withOptions(array $options)
    {
        $this->options = array_merge_recursive($options, $this->options);
        return $this;
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

        if (! isset($this->options['headers'][$name]) || $override) {
            $this->options['headers'][$name] = $value;
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

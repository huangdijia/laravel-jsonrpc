<?php

namespace Huangdijia\JsonRpc;

use Exception;

class Client
{
    const JSONRPC_VERSION = '2.0';
    /**
     * @var mixed 測試開關
     */
    private $debug = false;
    /**
     * @var string 請求地址
     */
    private $url = '';
    /**
     * @var int 請求編號
     */
    private $id = 0;
    /**
     * @var mixed 通知開關
     */
    private $notification = false;
    /**
     * @var array 请求头部
     */
    private $header = [];
    /**
     *
     * @var boolean 忽略ssl
     */
    private $isIgnoreSsl = false;
    /**
     * @param string $url 請示地址
     * @param bool $debug 測試開關
     */
    public function __construct($url, $debug = false)
    {
        // 服务器地址
        $this->url = $url;
        // 请求头部
        $this->header = ['Content-type' => 'application/json'];
        // 调试状态
        $this->debug = empty($debug) ? false : true;
        // 请求 id
        $this->id = 1;
        // 默认忽略ssl
        if (substr($this->url, 0, 5) == 'https') {
            $this->isIgnoreSsl = true;
        }
    }

    /**
     * @param string $name 名称
     * @param string $value 值
     */
    public function setHeader($name, $value = '')
    {
        if (in_array(strtolower($name), ['content-type', 'content-length'])) {
            return $this;
        }

        $this->header[$name] = $value;

        return $this;
    }

    /**
     * 通知開關
     * @param bool $notification
     */
    public function setRPCNotification($notification)
    {
        $this->notification = empty($notification) ? false : true;

        return $this;
    }

    /**
     * 是否忽略ssl
     *
     * @param boolean $bool
     */
    public function setIgnoreSsl($bool = false)
    {
        $this->isIgnoreSsl = $bool;

        return $this;
    }

    /**
     * @param string $method 請示方法
     * @param array $params 請求參數
     * @return mixed
     */
    public function __call($method, $params)
    {
        // 检测方法类型，必须为字符
        if (!is_scalar($method)) {
            throw new Exception('Method name has no scalar value');
        }

        // 检查参数类型，必须为数组
        if (is_array($params)) {
            $params = array_values($params);
        } else {
            throw new Exception('Params must be given as array');
        }

        // 设置通知
        if ($this->notification) {
            $currentId = null;
        } else {
            $currentId = $this->id;
        }

        // 封闭参数
        $request = [
            'jsonrpc' => self::JSONRPC_VERSION,
            'method'  => $method,
            'params'  => $params,
            'id'      => $currentId,
        ];

        $request = json_encode($request);

        $this->debug && $this->debug .= '***** Request *****' . "\n" . $request . "\n" . '***** End Of request *****' . "\n\n";

        // 封装 HTTP HEADER
        $header = '';

        foreach ($this->header as $key => $value) {
            $header .= "{$key}: {$value}\r\n";
        }

        $header .= "Content-Length: " . strlen($request);

        // 封装 HTTP 请求
        $opts = [
            'http' => [
                'method'  => 'POST',
                'header'  => $header,
                'content' => $request,
            ],
        ];

        if ($this->isIgnoreSsl) {
            $opts["ssl"] = [
                "verify_peer"      => false,
                "verify_peer_name" => false,
            ];
        }

        $context = stream_context_create($opts);

        if ($fp = fopen($this->url, 'r', false, $context)) {
            $response = '';

            while ($row = fgets($fp)) {
                $response .= trim($row) . "\n";
            }

            $this->debug && $this->debug .= '***** Server response *****' . "\n" . $response . '***** End of server response *****' . "\n";

            $response = json_decode($response, true);
        } else {
            throw new Exception('Unable to connect to ' . $this->url);
        }

        // 调试输出
        if ($this->debug) {
            info($this->debug);
        }

        // 最后检查返回结果
        if (!$this->notification) {
            // 检查请求 id
            if (isset($response['id']) && $response['id'] != $currentId) {
                throw new Exception('Incorrect response id (request id: ' . $currentId . ', response id: ' . $response['id'] . ')');
            }

            if (isset($response['error']) && !is_null($response['error'])) {
                throw new Exception('Request error: ' . $response['error']);
            }

            if (!isset($response['result'])) {
                throw new Exception("Error response[result]", 1);
            }

            return $response['result'];
        }

        return true;
    }
}

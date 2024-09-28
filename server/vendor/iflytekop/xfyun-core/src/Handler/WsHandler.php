<?php

/**
 * Copyright 1999-2021 iFLYTEK Corporation

 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace IFlytek\Xfyun\Core\Handler;

use IFlytek\Xfyun\Core\Traits\JsonTrait;
use WebSocket\Client;
use WebSocket\Exception;
use GuzzleHttp\Psr7\Response;
use Psr\Log\{LoggerAwareInterface, LoggerInterface, NullLogger};

/**
 * WebSocket处理类
 *
 * @author guizheng@iflytek.com
 */
class WsHandler implements LoggerAwareInterface
{
    use JsonTrait;

    /**
     * @var WebSocket\Client ws client
     */
    private $client;

    /**
     * @var string 发送的字符串
     */
    private $input;

    /**
     * @var LoggerInterface or null 日志处理
     */
    private $logger;

    public function __construct($uri, $input, $timeout = 300, $logger = null)
    {
        $this->client = new Client($uri);
        $this->client->setTimeout($timeout);
        $this->input = $input;
        $this->logger = $logger ?: new NullLogger();
    }

    /**
     * 发送并等待获取返回
     *
     * 这是一个同步阻塞的过程，调用将持续到结果返回或者出现超时
     */
    public function sendAndReceive()
    {
        $result = '';
        try {
            $this->logger->info("Start to send data, input: {$this->input}");
            $this->client->send($this->input);
            $printSid = true;
            while (true) {
                $message = $this->jsonDecode($this->client->receive());
                if ($message->code !== 0) {
                    throw new \Exception(json_encode($message));
                }
                if ($printSid) {
                    $this->logger->info("Receiving data, sid-[{$message->sid}]");
                    $printSid = false;
                }
                switch ($message->data->status) {
                    case 1:
                        $result .= base64_decode($message->data->audio);
                        break;
                    case 2:
                        $result .= base64_decode($message->data->audio);
                        break 2;
                }
            }
            $this->logger->info("Receive data successfully, total length: " . strlen($result));
            return new Response(200, [], $result);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function send($message = null)
    {
        try {
            if (empty($message)) {
                if (!empty($this->input)) {
                    $message = $this->input;
                } else {
                    throw new Exception();
                }
            }
            return $this->client->send($message);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function receive()
    {
        try {
            return $this->client->receive();
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function setLogger(LoggerInterface $logger = null)
    {
        $this->logger = $logger ?: new NullLogger();
    }

}

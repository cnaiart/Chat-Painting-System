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

namespace IFlytek\Xfyun\Core;

/**
 * Http客户端
 *
 * @author guizheng@iflytek.com
 */

use Exception;
use GuzzleHttp\Psr7\Response;
use IFlytek\Xfyun\Core\Handler\Guzzle6HttpHandler;
use IFlytek\Xfyun\Core\Handler\Guzzle7HttpHandler;
use IFlytek\Xfyun\Core\Traits\SignTrait;
use IFlytek\Xfyun\Core\Traits\DecideRetryTrait;
use IFlytek\Xfyun\Core\Handler\HttpHandlerFactory;
use Psr\Http\Message\RequestInterface;
use GuzzleHttp\Psr7;

class HttpClient
{
    use SignTrait;
    use DecideRetryTrait;

    const MAX_DELAY_MICROSECONDS = 60000000;

    /**
     * @var Guzzle6HttpHandler|Guzzle7HttpHandler
     */
    private $httpHandler;

    /**
     * @var array 要添加的头部信息
     */
    private $httpHeaders;

    /**
     * @var int 超时时间
     */
    private $requestTimeout;

    /**
     * @var int 重试次数
     */
    private $retries;

    /**
     * @var int 重试次数
     */
    private $decideRetryFunction;

    /**
     * @var int 重试次数
     */
    private $delayFunction;

    /**
     * @var int 重试次数
     */
    private $calcDelayFunction;

    public function __construct($config)
    {
        $config += [
            'httpHandler' => null,
            'httpHeaders' => [],
            'requestTimeout' => 3000,
            'retries' => 3,
            'decideRetryFunction' => null,
            'delayFunction' => null,
            'calcDelayFunction' => null
        ];

        $this->httpHandler = $config['httpHandler'] ?: HttpHandlerFactory::build();
        $this->httpHeaders = $config['httpHeaders'];
        $this->retries = $config['retries'];
        $this->decideRetryFunction = $config['decideRetryFunction'] ?: $this->getDecideRetryFunction();
        $this->calcDelayFunction = $config['calcDelayFunction'] ?: [$this, 'calculateDelay'];
        $this->delayFunction = $config['delayFunction'] ?: static function ($delay) {
            usleep($delay);
        };
    }

    /**
     * 发送请求，并接受返回
     *
     * @param   RequestInterface $request
     * @param   array $options 请求的配置参数
     * @return  Response
     * @throws  Exception
     */
    public function sendAndReceive(RequestInterface $request, array $options = [])
    {
        try {
            $delayFunction = $this->delayFunction;
            $calcDelayFunction = $this->calcDelayFunction;
            $retryAttempt = 0;

            while (true) {
                try {
                    return call_user_func_array($this->httpHandler, [$this->applyHeaders($request), $options]);
                } catch (Exception $exception) {
                    if ($this->decideRetryFunction) {
                        if (!call_user_func($this->decideRetryFunction, $exception)) {
                            throw $exception;
                        }
                    }

                    if ($retryAttempt >= $this->retries) {
                        break;
                    }

                    $delayFunction($calcDelayFunction($retryAttempt));
                    $retryAttempt++;
                }
            }

            throw $exception;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 将头部信息添加到请求对象中
     *
     * @param   array $request 请求对象
     * @return  RequestInterface
     */
    private function applyHeaders($request)
    {
        return Psr7\modify_request($request, ['set_headers' => $this->httpHeaders]);
    }

    /**
     * 根据重试次数计算下次重试延迟时间
     *
     * @param   int $attempt 重试次数
     * @return  int
     */
    public static function calculateDelay($attempt)
    {
        return min(
            mt_rand(0, 1000000) + (pow(2, $attempt) * 1000000),
            self::MAX_DELAY_MICROSECONDS
        );
    }
}

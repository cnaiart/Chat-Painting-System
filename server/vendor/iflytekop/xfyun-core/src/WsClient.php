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
 * WebSocket客户端
 *
 * @author guizheng@iflytek.com
 */
class WsClient
{
    /** @var WsHandler */
    private $handler;

    public function __construct($config)
    {
        $config += [
            'handler' => null,
        ];
        $this->handler = $config['handler'];
    }

    /**
     * 发送请求，并接受返回
     *
     * @return  Response
     */
    public function sendAndReceive()
    {
        try {
            return call_user_func_array([$this->handler, 'sendAndReceive'], []);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function send($message = null)
    {
        try {
            return call_user_func_array([$this->handler, 'send'], [$message]);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function receive()
    {
        try {
            return call_user_func_array([$this->handler, 'receive'], []);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}

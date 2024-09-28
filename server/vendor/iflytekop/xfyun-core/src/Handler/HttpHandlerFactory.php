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

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

/**
 * HttpHandler工厂类
 *
 * @author guizheng@iflytek.com
 */
class HttpHandlerFactory
{
    /**
     * 根据安装的GuzzleHttp版本获取默认的处理类
     *
     * @param   ClientInterface $client
     * @return  Guzzle6HttpHandler|Guzzle7HttpHandler
     * @throws  \Exception
     */
    public static function build(ClientInterface $client = null)
    {
        $client = $client ?: new Client();

        $version = null;
        if (defined('GuzzleHttp\ClientInterface::MAJOR_VERSION')) {
            $version = ClientInterface::MAJOR_VERSION;
        } elseif (defined('GuzzleHttp\ClientInterface::VERSION')) {
            $version = (int) substr(ClientInterface::VERSION, 0, 1);
        }

        switch ($version) {
            case 6:
                return new Guzzle6HttpHandler($client);
            case 7:
                return new Guzzle7HttpHandler($client);
            default:
                throw new \Exception('GuzzleHttp版本暂不支持');
        }
    }
}

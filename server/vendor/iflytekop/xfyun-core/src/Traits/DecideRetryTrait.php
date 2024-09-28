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

namespace IFlytek\Xfyun\Core\Traits;

use GuzzleHttp\Exception\RequestException;

/**
 * 提供一个方法，决定是否进行重试
 *
 * @author guizheng@iflytek.com
 */
trait DecideRetryTrait
{
    use JsonTrait;

    /**
     * @var array
     */
    private $httpRetryCodes = [
        500,
        502,
        503
    ];

    /**
     * @var array
     */
    private $httpRetryMessages = [
        'retry later'
    ];

    /**
     * 返回一个callable变量，作用是决定是否重试
     *
     * @param   bool $shouldRetryMessages 是否要根据message决定重试与否
     * @return  callable
     */
    private function getDecideRetryFunction($shouldRetryMessages = true)
    {
        $httpRetryCodes = $this->httpRetryCodes;
        $httpRetryMessages = $this->httpRetryMessages;

        return function (\Exception $ex) use ($httpRetryCodes, $httpRetryMessages, $shouldRetryMessages) {
            $statusCode = $ex->getCode();

            if (in_array($statusCode, $httpRetryCodes)) {
                return true;
            }

            if (!$shouldRetryMessages) {
                return false;
            }

            $message = ($ex instanceof RequestException && $ex->hasResponse())
                ? (string) $ex->getResponse()->getBody()
                : $ex->getMessage();

            try {
                $message = $this->jsonDecode(
                    $message,
                    true
                );
            } catch (\InvalidArgumentException $ex) {
                return false;
            }

            if (!isset($message['errors'])) {
                return false;
            }

            foreach ($message['errors'] as $error) {
                if (in_array($error['reason'], $httpRetryMessages)) {
                    return true;
                }
            }

            return false;
        };
    }
}

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

namespace Iflytek\Xfyun\Core\Traits;

/**
 * 提供对原生json_encode和json_decode的封装，以便在发生错误时抛出一个异常
 *
 * @author guizheng@iflytek.com
 */
trait JsonTrait
{
    /**
     * @param   string  $json       待解码的字符串
     * @param   bool    $assoc      是否返回数组
     * @param   int     $depth      递归深度
     * @param   int     $options    json_decode的配置
     * @return  mixed
     * @throws  \InvalidArgumentException
     */
    private static function jsonDecode($json, $assoc = false, $options = 0, $depth = 512)
    {
        $data = json_decode($json, $assoc, $depth, $options);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \InvalidArgumentException(
                'json_decode error: ' . json_last_error_msg()
            );
        }

        return $data;
    }

    /**
     * @param   mixed   $value      待编码的变量
     * @param   int     $options    json_decode的配置
     * @param   int     $depth      递归深度
     * @return  string
     * @throws  \InvalidArgumentException
     */
    private static function jsonEncode($value, $options = 0, $depth = 512)
    {
        $json = json_encode($value, $options, $depth);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \InvalidArgumentException(
                'json_encode error: ' . json_last_error_msg()
            );
        }

        return $json;
    }
}

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

/**
 * 提供平台的签名方法
 *
 * @author guizheng@iflytek.com
 */
trait SignTrait
{

    /**
     * 根据secret对uri进行签名，返回签名后的uri
     *
     * @param   string  $uri    待签名的uri
     * @param   array   $secret 秘钥信息
     * @return  string
     */
    private static function signUriV1($uri, $secret)
    {
        $apiKey = $secret['apiKey'];
        $apiSecret = $secret['apiSecret'];
        $host = $secret['host'];
        $request_line = $secret['requestLine'];
        $date = empty($secret['date']) ? gmstrftime("%a, %d %b %Y %T %Z", time()) : $secret['date'];

        $signature_origin = "host: $host\ndate: $date\n$request_line";
        $signature_sha = hash_hmac('sha256', $signature_origin, $apiSecret, true);
        $signature = base64_encode($signature_sha);

        $authrization = base64_encode("api_key=\"$apiKey\",algorithm=\"hmac-sha256\",headers=\"host date request-line\",signature=\"$signature\"");
        $uri = $uri . '?' . http_build_query([
            'host' => $host,
            'date' => $date,
            'authorization' => $authrization
        ]);
        return $uri;
    }

    /**
     * 根据所提供信息返回签名
     *
     * @param   string  $appId      appid
     * @param   string  $secretKey  secretKey
     * @param   string  $timestamp  时间戳，不传的话使用系统时间
     * @return  string
     */
    public static function signV1($appId, $secretKey, $timestamp = null)
    {
        $timestamp = $timestamp ?: time();
        $baseString = $appId . $timestamp;
        $signa_origin = hash_hmac('sha1', md5($baseString), $secretKey, true);
        return base64_encode($signa_origin);
    }

    /**
     * https调用的鉴权参数构造
     *
     * @param   string  $appId      appId
     * @param   string  $apiKey     apiKey
     * @param   string  $curTime    curTime
     * @param   string  $param      param
     * @return  array
     */
    public static function signV2($appId, $apiKey, $param, $curTime = null)
    {
        if (empty($curTime)) {
            $curTime = time();
        }
        return [
            'X-Appid' => $appId,
            'X-CurTime' => $curTime,
            'X-Param' => base64_encode($param),
            'X-CheckSum' => md5($apiKey . $curTime . base64_encode($param))
        ];
    }
}

<?php

namespace app\common\service\draw\engine;

/**
 * 意间绘画
 * Class DrawYj
 * @package app\common\service\draw\engine
 */
class DrawYj extends DrawBase implements DrawInterface
{
    private $AK;
    private $SK;
    private $timestamp;

    public function __construct($apiKey = '', $apiSecret = '', $extra = [])
    {
        $this->baseUrl = 'http://api.yjai.art:8080/painting-open-api/site/';
        $this->AK = $apiKey;
        $this->SK = $apiSecret;
        $this->timestamp = time();
        $this->notifyHook = request()->domain() . '/api/draw/notifyYj';
        if (!empty($extra['notify_domain'])) {
            $this->notifyHook = $extra['notify_domain'] . '/api/draw/notifyYj';
        }
    }

    /**
     * @notes
     * @param array $params
     * @return array
     * @throws \Exception
     * @author mjf
     * @date 2023/11/6 16:33
     */
    public function imagine(array $params)
    {
        $url = $this->baseUrl . "put_task";
        $data = array(
            'apikey' => $this->AK,
            'callback_type' => 'end',
            'callback_url' => $this->notifyHook,
            'timestamp' => $this->timestamp,

            // 关键词
            'prompt' => $params['prompt'],
            // 比例
            'ratio' => $params['scale'],
            // 风格字段
            'style' => $params['style'],
            // 绘画引擎
            'engine' => $params['engine'] ?? 'stable_diffusion',
            // 参考图
            'init_image' => $params['image_base'] ?? '',
        );
        $response = $this->httpPost($url, $data);
        return $this->getResponseData($response);
    }

    /**
     * @notes 任务详情
     * @param $uuid
     * @return array
     * @throws \Exception
     * @author mjf
     * @date 2023/11/6 16:34
     */
    public function detail($uuid)
    {
        $url = $this->baseUrl . "show_task_detail";
        $data = array(
            'apikey' => $this->AK,
            'timestamp' => $this->timestamp,
            'uuid' => $uuid,
        );
        $response = $this->httpPost($url, $data);
        return $this->getResponseData($response);
    }

    // 获取完成的任务
    public function showCompleteTasks()
    {
        $url = $this->baseUrl . "show_complete_tasks";
        $data = array(
            'apikey' => $this->AK,
            'timestamp' => $this->timestamp
        );
        $response = $this->httpPost($url, $data);
        return $this->getResponseData($response);
    }

    // 获取用户信息
    public function getUserInfo()
    {
        $url = $this->baseUrl . "getUserInfo";
        $data = array(
            'apikey' => $this->AK,
            'timestamp' => $this->timestamp,
        );
        $response = $this->httpPost($url, $data);
        return $this->getResponseData($response);
    }

    /**
     * @notes 签名
     * @param array $data
     * @return string
     * @author mjf
     * @date 2023/11/1 17:17
     */
    private function getSign($data = array())
    {
        $arr = array_merge(array('apisecret' => $this->SK), $data);
        ksort($arr);
        $param = [];
        foreach ($arr as $key => $val) {
            $param[] = $key . "=" . ($val);
        }
        $sParam = join("&", $param);
        return md5($sParam);
    }

    public function imagineUv(array $params)
    {
    }

    /**
     * @notes 风格选项
     * @return array
     * @throws \Exception
     * @author mjf
     * @date 2023/11/3 17:00
     */
    public function getSelector()
    {
        $url = $this->baseUrl . "get_draw_selector4";
        $data = array(
            'apikey' => $this->AK,
            'timestamp' => $this->timestamp,
        );
        $response = $this->httpPost($url, $data);
        return $this->getResponseData($response);
    }

    /**
     * @notes post请求
     * @param $url
     * @param $param
     * @param false $jsonPost
     * @param false $postFile
     * @return false|mixed
     * @author mjf
     * @date 2023/11/1 17:23
     */
    private function httpPost($url, $param, $jsonPost = false, $postFile = false)
    {
        $oCurl = curl_init();
        if (stripos($url, "https://") !== FALSE) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1);
        }
        $httpHeaders = array("Content-Type:application/x-www-form-urlencoded", "sign:" . $this->getsign($param));

        if ($jsonPost) {
            $strPOST = str_replace('\/', '/', json_encode($param));
        } elseif (is_string($param) || $postFile) {
            $strPOST = $param;
        } else {
            $aPOST = array();
            foreach ($param as $key => $val) {
                $aPOST[] = $key . "=" . ($val);
            }
            $strPOST = join("&", $aPOST);
        }

        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_POST, true);
        curl_setopt($oCurl, CURLOPT_HTTPHEADER, $httpHeaders);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS, $strPOST);

        $sContent = curl_exec($oCurl);
        $sContent = json_decode($sContent, true);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        return $sContent;
    }

    /**
     * @notes 请求结果
     * @param $response
     * @return array
     * @throws \Exception
     * @author mjf
     * @date 2023/11/3 17:00
     */
    public function getResponseData($response): array
    {
        if (isset($response['status']) && $response['status'] != 0) {
            $errMsg = !empty($response['reason']) ? $response['reason'] : '请求失败';
            throw new \Exception($errMsg);
        }

        return $response['data'] ?? [];
    }

}
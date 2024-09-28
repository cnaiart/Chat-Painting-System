<?php

namespace app\common\service\qrcode\engine;

use WpOrg\Requests\Requests;

/**
 * mewx-星月熊
 * Class QrCodeMewx
 * @package app\common\service\draw\engine
 */
class QrcodeMewx extends QrcodeBase implements QrcodeInterface
{

    protected string $apiToken;

    public function __construct(string $apiToken = '', string $proxyUrl = '')
    {
        $this->headers = [
            'Content-Type' => 'application/json',
            'Authorization' => "Bearer " . $apiToken,
        ];
        $this->baseUrl = "https://open-qr.mewx.art";
        if (!empty($proxyUrl)) {
            $this->baseUrl = $proxyUrl;
        }
        $this->apiToken = $apiToken;
        $this->notifyHook = request()->domain() . '/api/qrcode/notifyMewx';
    }

    /**
     * @notes 生成二维码
     * @param array $params
     * @return array
     * @throws \Exception
     * @author mjf
     * @date 2023/10/13 14:57
     */
    public function imagine($params)
    {
        $url = $this->baseUrl . '/api/v1/images/generate';
        $params = [
            // 关键词
            'prompt' => $params['prompt'],
            // 反词
            'negative_prompt' => $params['negative_prompt'] ?? '',
            // 二维码内容
            'qr_content' => $params['qr_content'] ?? '',
            // 模型id
            'model' => $params['model'] ?? '',
            // 二维码图片
            'qr_image' => $params['qr_image'] ?? '',
            // 模板id
            'template_id' => $params['template_id'] ?? '',
            'callback_url' => $this->notifyHook,
        ];
        $response = Requests::post($url, $this->headers, json_encode($params));
        return $this->getResponseData($response);
    }

    /**
     * @notes 查询详情
     * @param int $imgId
     * @return array
     * @throws \Exception
     * @author mjf
     * @date 2023/10/13 14:56
     */
    public function detail($imgId)
    {
        $url = $this->baseUrl . '/api/v1/images/detail?img_uuid=' . $imgId;
        $response = Requests::get($url, $this->headers);
        return $this->getResponseData($response);
    }

    /**
     * @notes 查询余额
     * @return array
     * @throws \Exception
     * @author mjf
     * @date 2023/10/13 14:56
     */
    public function count()
    {
        $url = $this->baseUrl . '/api/v1/user/count';
        $response = Requests::get($url, $this->headers);
        return $this->getResponseData($response);
    }

    // 模板
    public function template($type = 'qrcode')
    {
        $url = $this->baseUrl . '/api/v1/template/list?type=' . $type;
        $response = Requests::get($url, $this->headers);
        return $this->getResponseData($response);
    }

    // 模型
    public function model($type = 'qrcode')
    {
        $url = $this->baseUrl . '/api/v1/model/list?type=' . $type;
        $response = Requests::get($url, $this->headers);
        return $this->getResponseData($response);
    }

    /**
     * @notes 请求结果
     * @param $response
     * @return array
     * @throws \Exception
     * @author mjf
     * @date 2023/10/13 14:56
     */
    public function getResponseData($response): array
    {
        $response = json_decode($response->body, true);

        if (!isset($response['code']) || $response['code'] != 0) {
            $errMsg = !empty($response['message']) ? $response['message'] : "";
            if (empty($errMsg)) {
                $errMsg = !empty($response['msg']) ? $response['msg'] : '提交失败';
            }
            throw new \Exception($errMsg);
        }

        return $response;
    }


}
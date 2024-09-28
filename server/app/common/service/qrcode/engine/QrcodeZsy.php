<?php

namespace app\common\service\qrcode\engine;

use WpOrg\Requests\Requests;

/**
 * 知数云艺术二维码
 * Class QrcodeZsy
 * @package app\common\service\draw\engine
 */
class QrcodeZsy extends QrcodeBase implements QrcodeInterface
{
    protected string $apiToken;

    public function __construct(string $apiToken)
    {
        $this->headers = [
            'content-type' => 'application/json',
            'accept' => 'application/json',
        ];
        $this->baseUrl = "https://api.zhishuyun.com/qrart/";
        $this->apiToken = $apiToken;
        $this->notifyHook = request()->domain() . '/api/qrcode/notifyZsy';
    }

    /**
     * @notes 生成图片
     * @param array $params
     * @return array
     * @throws \Exception
     * @author mjf
     * @date 2023/11/9 15:29
     */
    public function imagine($params)
    {
        $url = $this->baseUrl . "generate?token=" . $this->apiToken;
        $params = [
            'type' => $params['type'] ?? 'text',
            'content' => $params['qr_content'],
            'prompt' => $params['prompt'],
            'qrw' => 2,
            'preset' => !empty($params['template_id']) ? $params['template_id'] : '',
            'aspect_ratio' => !empty($params['aspect_ratio']) ? $params['aspect_ratio'] : '',
            'pixel_style' => !empty($params['pixel_style']) ? $params['pixel_style'] : '',
            'marker_shape' => !empty($params['marker_shape']) ? $params['marker_shape'] : '',
            'callback_url' => $this->notifyHook,
        ];
        $option = ['timeout' => 100, 'connect_timeout' => 100];
        $response = Requests::post($url, $this->headers, json_encode($params), $option);
        return $this->getResponseData($response);
    }

    /**
     * @notes 二维码详情
     * @param $imgId
     * @return mixed|void
     * @author 段誉
     * @date 2024/2/7 13:17
     */
    public function detail($imgId)
    {
        $headers = [
            'content-type' => 'application/json',
            'accept' => 'application/json',
        ];
        $url = $this->baseUrl . "tasks?token=" . $this->apiToken;
        $params = [
            'id' => $imgId,
            'action' => 'retrieve',
        ];
        $option = ['timeout' => 100, 'connect_timeout' => 100];
        $response = Requests::post($url, $headers, json_encode($params), $option);
        return json_decode($response->body, true);
    }


    /**
     * @notes 结果处理
     * @param $response
     * @return array
     * @throws \Exception
     * @author 段誉
     * @date 2023/6/19 11:27
     */
    public function getResponseData($response): array
    {
        $response = json_decode($response->body, true);

        if (empty($response['task_id'])) {
            $errMsg = !empty($response['detail']) ? $response['detail'] : '提交失败';
            throw new \Exception($errMsg);
        }

        if (isset($response['success']) && $response['success'] == false) {
            $errMsg = !empty($response['detail']) ? $response['detail'] : '提交失败';
            throw new \Exception($errMsg);
        }

        return $response;
    }


}
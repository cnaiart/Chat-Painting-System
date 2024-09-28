<?php

namespace app\common\service\draw\engine;

use WpOrg\Requests\Requests;

/**
 * Class DrawSD
 * @package app\common\service\draw\engine
 */
class DrawSd extends DrawBase implements DrawInterface
{
    public function __construct(string $apiUrl = '')
    {
        $this->baseUrl = $apiUrl;
        $this->headers = [
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * @notes 绘画
     * @param array $params
     * @return array
     * @throws \Exception
     * @author mjf
     * @date 2023/11/30 10:50
     */
    public function imagine(array $params)
    {
        $data = [
            'prompt' => $params['prompt'],
            'batch_size' => 1,
            'width' => $params['width'],
            'height' => $params['height'],
        ];

        if (!empty($params['engine'])) {
            $data['override_settings']['sd_model_checkpoint'] = $params['engine'];
        }

        // 文生图
        $url = $this->baseUrl . '/sdapi/v1/txt2img';
        if (!empty($params['image'])) {
            // 图生图
            $url = $this->baseUrl . '/sdapi/v1/img2img';
            $data['init_images'][] = $params['image'];
        }
        $options = [];
        $options['timeout'] = 300;
        $options['connect_timeout'] = 300;
        $response = Requests::post($url, $this->headers, json_encode($data), $options);
        return $this->getResponseData($response);
    }

    public function imagineUv(array $params)
    {
    }


    /**
     * @notes 获取模型
     * @return array
     * @throws \Exception
     * @author mjf
     * @date 2024/1/4 11:19
     */
    public function getModel()
    {
        $url = $this->baseUrl . '/sdapi/v1/sd-models';
        $response = Requests::get($url);
        return $this->getResponseData($response);
    }


    /**
     * @notes 请求结果
     * @param $response
     * @return array
     * @throws \Exception
     * @author mjf
     * @date 2023/11/30 10:50
     */
    public function getResponseData($response): array
    {
        $responseData = json_decode($response->body, true);
        if (!empty($responseData['detail'][0]['msg'])) {
            throw new \Exception($responseData['detail'][0]['msg']);
        }
        return $responseData;
    }
}
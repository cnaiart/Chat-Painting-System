<?php

namespace app\common\service\draw\engine;

use app\common\enum\DrawEnum;
use WpOrg\Requests\Requests;

/**
 * 知数云绘画
 * Class DrawZsy
 * @package app\common\service\draw
 */
class DrawZsy extends DrawBase implements DrawInterface
{
    protected string $apiToken;

    public function __construct(string $apiType, string $apiToken = '')
    {
        $this->headers = [
            'content-type' => 'application/json',
            'accept' => 'application/json',
        ];
        $baseUri = 'https://api.zhishuyun.com/midjourney';

        $this->baseUrl = match ($apiType) {
            DrawEnum::API_ZHISHUYUN_RELAX => $baseUri . '/imagine/relax',
            DrawEnum::API_ZHISHUYUN_TURBO => $baseUri . '/imagine/turbo',
            DrawEnum::API_ZHISHUYUN_FAST => $baseUri . '/imagine',
            default => $baseUri,
        };

        $this->apiToken = $apiToken;
        $this->notifyHook = request()->domain() . '/api/draw/notifyZsy';
    }

    /**
     * @notes 文生图,图生图 // $prompt 关键词  $imageId 图片id // 操作
     * @return array
     * @throws \Exception
     * @author 段誉
     * @date 2023/6/19 11:22
     */
    public function imagine($params)
    {
        $url = $this->baseUrl . "?token=" . $this->apiToken;
        $params = [
            'action' => $params['action'],
            'prompt' => $params['prompt'],
            'image_id' => $params['image_id'],
            'callback_url' => $this->notifyHook,
        ];
        $option = ['timeout' => 100, 'connect_timeout' => 100];
        $response = Requests::post($url, $this->headers, json_encode($params), $option);
        return $this->getResponseData($response);
    }

    /**
     * @notes 图片放大，变换
     * @param array $params
     * @return array
     * @throws \Exception
     * @author 段誉
     * @date 2023/7/17 9:56
     */
    public function imagineUv($params)
    {
        return $this->imagine($params);
    }

    /**
     * @notes
     * @param string $taskId
     * @return array
     * @throws \Exception
     * @author mjf
     * @date 2024/2/2 17:40
     */
    public function detail(string $taskId)
    {
        $headers = [
            'content-type' => 'application/json',
            'accept' => 'application/json',
        ];

        $url = "https://api.zhishuyun.com/midjourney/tasks";
        $params = [
            'id' => $taskId,
            'action' => 'retrieve',
        ];
        $option = ['timeout' => 100, 'connect_timeout' => 100];
        $response = Requests::post($url, $headers, json_encode($params), $option);
        return json_decode($response->body, true);
    }

    /**
     * @notes 流式输出
     * @param string $prompt
     * @param string $action
     * @param string $imageId
     * @return array|false
     * @throws \Exception
     * @author 段誉
     * @date 2023/6/20 16:31
     */
    public function drawStream(string $prompt, string $action = 'generate', string $imageId = "")
    {
        ignore_user_abort(true);

        $url = $this->baseUrl . "?token=" . $this->apiToken;

        $params = [
            'action' => $action,
            'prompt' => $prompt,
            'image_id' => $imageId,
            'timeout' => 1000,
//            'callback_url' => $this->notifyHook,
        ];

        header("Access-Control-Allow-Origin: *");
        header('X-Accel-Buffering: no');
        header('Content-type: text/event-stream');
        header('Cache-Control: no-cache');

        return self::postCurl($url, $params);
    }

    /**
     * @notes 提交请求
     * @param string $url
     * @param string $param
     * @return array
     * @throws \Exception
     * @author 段誉
     * @date 2023/6/25 15:38
     */
    public static function postCurl($url = '', $param = '')
    {
        $flag = true;
        $content = [];
        $callback = function ($ch, $data) use (&$content, &$flag) {
            $result = @json_decode($data, true);

            if (!empty($result['code'])) {
                $flag = !empty($result['detail']) ? $result['detail'] : "绘图异常";
            } else {
                $content = [
                    'task_id' => $result['task_id'] ?? '',
                    'image_id' => $result['image_id'] ?? '',
                    'image_url' => '',
                    'progress' => $result['progress'] ?? 0,
                    'notify_snap' => $data,
                    'actions' => $result['actions'] ?? ''
                ];

                if (!empty($result['progress']) && $result['progress'] == 100) {
                    $content['image_url'] = $result['image_url'];
                }
                echo $data;
                ob_flush();
                flush();
            }
            return strlen($data);
        };

        $curlInfo = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($param),
            CURLOPT_WRITEFUNCTION => $callback,
            CURLOPT_TIMEOUT => 600,
            CURLOPT_HTTPHEADER => array("accept: application/x-ndjson", "content-type: application/json"),
        ];
        $curl = curl_init();
        curl_setopt_array($curl, $curlInfo);
        curl_exec($curl);
        curl_close($curl);

        if (true !== $flag) {
            throw new \Exception($flag);
        }

        return $content;
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
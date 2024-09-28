<?php

namespace app\common\service\draw\engine;

use app\api\logic\MidjourneyLogic;

/**
 * 官方直连-MJai绘画
 * Class DrawMddai
 * @package app\common\service\draw\engine
 */
class DrawMdd extends DrawBase implements DrawInterface
{
    protected string $apiToken;

    public function __construct(string $apiToken = '')
    {
        $this->apiToken = $apiToken;
        $this->notifyHook = request()->domain() . '/api/draw/notifyMdd';
    }

    /**
     * @notes 文生图，图生图
     * @param array $params
     * @return array
     * @throws \Exception
     * @author 段誉
     * @date 2023/8/3 15:06
     */
    public function imagine($params)
    {
        $params = [
            'prompt' => $params['prompt'],
            'image_base' => $params['image_base'] ?? '',
            'notify_hook' => $this->notifyHook,
            'token' => $this->apiToken,
        ];
        $result = MidjourneyLogic::imagine($params);
        if ($result === false) {
            throw new \Exception(MidjourneyLogic::getError());
        }
        return $result;
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
        $params = [
            'action' => $params['action'],
            'task_id' => $params['task_id'],
            'index' => $params['index'],
            'notify_hook' => $this->notifyHook,
            'token' => $this->apiToken,
        ];
        $result = MidjourneyLogic::change($params);
        if ($result === false) {
            throw new \Exception(MidjourneyLogic::getError());
        }
        return $result;
    }


}
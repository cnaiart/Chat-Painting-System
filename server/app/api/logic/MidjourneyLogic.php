<?php
// +----------------------------------------------------------------------
// | likeadmin快速开发前后端分离管理后台（PHP版）
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | gitee下载：https://gitee.com/likeshop_gitee/likeadmin
// | github下载：https://github.com/likeshop-github/likeadmin
// | 访问官网：https://www.likeadmin.cn
// | likeadmin团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeadminTeam
// +----------------------------------------------------------------------

namespace app\api\logic;

use app\common\enum\DrawTaskEnum;
use app\common\logic\BaseLogic;
use app\common\model\draw\DrawTask;
use app\common\service\QueueService;

/**
 * Class MidjourneyLogic
 * @package app\api\logic
 */
class MidjourneyLogic extends BaseLogic
{
    /**
     * @notes 文生图，图生图
     * @param array $params
     * @return bool|array
     * @author mjf
     * @date 2023/8/3 10:54
     */
    public static function imagine(array $params): bool|array
    {
        try {
            $token = $params['token'] ?? '';
            if (empty($token)) {
                throw new \Exception("token参数缺失");
            }

            // 创建任务
            $taskId = self::genUniqueId();

            // 提示词
            $prompt = self::filterPrompt($params['prompt']);

            $data = [
                'task_id' => $taskId,
                'action' => DrawTaskEnum::ACTION_GENERATE,
                'image_base' => $params['image_base'] ?? '',
                'notify_hook' => $params['notify_hook'] ?? '',
                'prompt' => $prompt,
                'prompt_desc' => '<!id:' . $taskId . '> ' . $prompt,
                'request_snap' => self::getRequestSnap($params),
                'token' => $token,
                'status' => DrawTaskEnum::STATUS_NOT_START,
            ];

            if (!empty($params['image_base'])) {
                $data['image_base'] = $params['image_base'];
            }

            $task = DrawTask::create($data);

            // 已有任务数量
            $taskCount = DrawTask::where('status', DrawTaskEnum::STATUS_NOT_START)->count();
            if ($taskCount > 10) {
                // 提交到队列等待处理
                QueueService::pushMj($task->toArray(), 10);
            } else {
                // 提交到队列等待处理
                QueueService::pushMj($task->toArray());
            }

            return [
                'task_id' => $task['task_id'],
            ];
        } catch (\Exception $e) {
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * @notes 放大，变换
     * @param array $params
     * @return array|false
     * @author mjf
     * @date 2023/8/3 10:54
     */
    public static function change(array $params)
    {
        try {
            $token = $params['token'] ?? '';
            if (empty($token)) {
                throw new \Exception("token参数缺失");
            }

            // 查询原任务
            $existTask = DrawTask::where('task_id', $params['task_id'])
                ->findOrEmpty();

            if ($existTask->isEmpty()) {
                throw new \Exception("绘画异常");
            }

            if ($existTask->status != DrawTaskEnum::STATUS_SUCCESS) {
                throw new \Exception("关联任务状态异常");
            }

            $allowedActions = [DrawTaskEnum::ACTION_GENERATE, DrawTaskEnum::ACTION_VARIATION];
            if (!in_array($existTask->action, $allowedActions)) {
                throw new \Exception("关联任务不允许执行变化");
            }

            // 图片索引
            $index = $params['index'] ?? 1;

            if ($params['action'] == DrawTaskEnum::ACTION_UPSAMPLE) {
                $repeatTask = DrawTask::where([
                    'action' => DrawTaskEnum::ACTION_UPSAMPLE,
                    'prompt_desc' => self::promptReplace($existTask['prompt_desc']),
                ])->findOrEmpty();
                if (!$repeatTask->isEmpty() && $index == $repeatTask['index']) {
                    throw new \Exception("已有重复任务");
                }
            }

            // 创建任务
            $task = DrawTask::create([
                'task_id' => self::genUniqueId(),
                'action' => $params['action'],
                'index' => $index,
                'image_msg_id' => $existTask['msg_id'] ?? '',
                'image_msg_hash' => $existTask['msg_hash'] ?? '',
                'prompt' => self::promptReplace($existTask['prompt']),
                'prompt_desc' => self::promptReplace($existTask['prompt_desc']),
                'notify_hook' => $params['notify_hook'] ?? '',
                'request_snap' => self::getRequestSnap($params),
                'token' => $token,
                'status' => DrawTaskEnum::STATUS_NOT_START,
            ]);

            // 已有任务数量
            $taskCount = DrawTask::where('status', DrawTaskEnum::STATUS_NOT_START)->count();
            if ($taskCount > 10) {
                // 提交到队列等待处理
                QueueService::pushMj($task->toArray(), 10);
            } else {
                // 提交到队列等待处理
                QueueService::pushMj($task->toArray());
            }

            return [
                'task_id' => $task['task_id'],
            ];
        } catch (\Exception $e) {
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * @notes 唯一任务id
     * @param int $length
     * @return string
     * @throws \Exception
     * @author mjf
     * @date 2023/8/1 16:11
     */
    private static function genUniqueId($length = 16)
    {
        $characters = '0123456789';
        $uniqueId = '';

        $max = strlen($characters) - 1;
        for ($i = 0; $i < $length; $i++) {
            if ($i == 0) {
                $item = $characters[random_int(1, $max)];
            } else {
                $item = $characters[random_int(0, $max)];
            }
            $uniqueId .= $item;
        }

        $task = DrawTask::where('task_id', $uniqueId)->findOrEmpty();
        if (!$task->isEmpty()) {
            return self::genUniqueId(16);
        }

        return $uniqueId;
    }

    /**
     * @notes 请求快照
     * @param array $params
     * @return false|string
     * @author mjf
     * @date 2023/8/1 16:06
     */
    private static function getRequestSnap(array $params)
    {
        $requestSnap = array_merge($params, [
            'request_ip' => request()->ip(),
            'request_time' => time()
        ]);

        return json_encode($requestSnap, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @notes 过滤提示词空格
     * @param $str
     * @return array|string|string[]|null
     * @author mjf
     * @date 2023/8/1 18:33
     */
    private static function filterPrompt($str)
    {
        $str = preg_replace('/\s+/', ' ', $str);
        return trim($str);
    }

    /**
     * @notes 图片变化时去除慢速标签
     * @param $prompt
     * @return array|string|string[]
     * @author mjf
     * @date 2023/8/2 17:57
     */
    private static function promptReplace($prompt)
    {
        return str_replace("--relax", '', $prompt);
    }


}
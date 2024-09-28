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

namespace app\common\service\discord\event;

use app\common\enum\DrawTaskEnum;
use app\common\model\draw\DrawTask;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use think\facade\Log;

/**
 * Class VariationHandler
 * @package app\common\service\discord\event
 */
class VariationHandler extends MessageHandler
{

    /**
     * @notes 图片变化
     * @param Message $message
     * @param Discord $discord
     * @author mjf
     * @date 2023/8/2 18:55
     */
    public function handle(Message $message, Discord $discord)
    {
        if ($this->isIgnoreMessage($message, $discord)) {
            return;
        }

        $parseData = $this->parse($message->content);
        if (is_null($parseData)) {
            return;
        }

        try {
            $taskModel = new DrawTask();
            // 匹配信息
            if (str_contains($parseData['status'], "Waiting to start")) {
                // 开始
                $where = [
                    ['prompt_desc', 'like', '%' . $this->getTaskIdBPrompt($parseData['prompt']) . '%'],
                    ['action', '=', DrawTaskEnum::ACTION_VARIATION],
                    ['status', '=', DrawTaskEnum::STATUS_SUBMIT]
                ];
                $task = $taskModel->where($where)->order('id desc')->findOrEmpty();

                if ($task->isEmpty()) {
                    Log::record("discord-vMsg-create-not-find1111:"
                        . "prompt_desc:" . $parseData['prompt']
                        . '--action:' . DrawTaskEnum::ACTION_VARIATION
                        . '--status:' . DrawTaskEnum::STATUS_SUBMIT);
                    return;
                }

                $task->status = DrawTaskEnum::STATUS_IN_PROGRESS;
                $task->start_time = time();
                $task->save();
            } else {
                // 完成
                $where = [
                    ['prompt_desc', 'like', '%' . $this->getTaskIdBPrompt($parseData['prompt']) . '%'],
                    ['action', '=', DrawTaskEnum::ACTION_VARIATION],
                    ['status', 'in', [DrawTaskEnum::STATUS_SUBMIT, DrawTaskEnum::STATUS_IN_PROGRESS]]
                ];
                $task = $taskModel->where($where)->order('id desc')->findOrEmpty();

                if ($task->isEmpty()) {
                    Log::record("discord-vMsg-create-not-find2222:"
                        . "prompt_desc:" . $parseData['prompt']
                        . '--action:' . DrawTaskEnum::ACTION_VARIATION
                        . '--status:' . DrawTaskEnum::STATUS_SUBMIT . '--' . DrawTaskEnum::STATUS_IN_PROGRESS);
                    return;
                }

                $attachments = $message->attachments->toArray();
                if (!empty($attachments)) {
                    $this->taskSuccess($task, $message);
                } else {
                    $this->taskFail($task, $message);
                }
            }
        } catch (\Exception $e) {
            Log::record("discord-vMsg-error" . $e->getMessage() . '--' . $e->getFile() . '--' . $e->getLine());
        }
    }

    /**
     * @notes 解析数据
     * @param $content
     * @return array|null
     * @author mjf
     * @date 2023/8/2 11:37
     */
    private function parse($content)
    {
        $pattern1 = '/Making variations for image #(\d) with prompt \*\*(.*?)\*\* - <@\d+> \((.*?)\)/';
        $pattern2 = '/\*\*(.*?)\*\* - Variations by <@\d+> \((.*?)\)/';
        $pattern3 = '/\*\*(.*?)\*\* - Variations \(.*?\) by <@\d+> \((.*?)\)/';

        preg_match($pattern1, $content, $matches);
        if (!empty($matches)) {
            return [
                'prompt' => $matches[2],
                'status' => $matches[3],
            ];
        }

        $parseData = $this->parseContent($content, $pattern2);
        if ($parseData === null) {
            $parseData = $this->parseContent($content, $pattern3);
        }

        return $parseData;
    }

    /**
     * @notes 数据解析
     * @param $content
     * @param $regex
     * @return array|null
     * @author mjf
     * @date 2023/8/2 18:56
     */
    protected function parseContent($content, $regex)
    {
        if (empty($content)) {
            return null;
        }

        preg_match($regex, $content, $matches);
        if (empty($matches)) {
            return null;
        }

        return [
            'prompt' => $matches[1],
            'status' => $matches[2],
        ];
    }

}
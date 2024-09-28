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
 * Class ImagineHandler
 * @package app\common\service\discord
 */
class ImagineHandler extends MessageHandler
{

    /**
     * @notes 生图处理
     * @param Message $message
     * @param Discord $discord
     * @author mjf
     * @date 2023/8/2 11:14
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
                    ['action', '=', DrawTaskEnum::ACTION_GENERATE],
                    ['status', '=', DrawTaskEnum::STATUS_SUBMIT]
                ];
                $task = $taskModel->where($where)->order('id desc')->findOrEmpty();

                if ($task->isEmpty()) {
                    Log::record("discord-imagineMsg-create-not-find1111:"
                        . "prompt_desc:" . $parseData['prompt']
                        . '--action:' . DrawTaskEnum::ACTION_GENERATE
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
                    ['action', '=', DrawTaskEnum::ACTION_GENERATE],
                    ['status', 'in', [DrawTaskEnum::STATUS_SUBMIT, DrawTaskEnum::STATUS_IN_PROGRESS]]
                ];
                $task = $taskModel->where($where)->order('id desc')->findOrEmpty();

                if ($task->isEmpty()) {
                    Log::record("discord-imagineMsg-create-not-find2222:"
                        . "prompt_desc:" . $parseData['prompt']
                        . '--action:' . DrawTaskEnum::ACTION_GENERATE
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
            Log::record("discord-imagineMsg-error" . $e->getMessage() . '--' . $e->getFile() . '--' . $e->getLine());
        }
    }

    /**
     * @notes 解析数据
     * @param $content
     * @return array|null
     * @author mjf
     * @date 2023/8/2 14:56
     */
    protected function parse($content)
    {
        if (empty($content)) {
            return null;
        }

        $regex = '/\*\*(.*?)\*\* - <@\d+> \((.*?)\)/';
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
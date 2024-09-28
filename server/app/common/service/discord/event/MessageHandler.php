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

use app\api\logic\DrawLogic;
use app\common\enum\DrawTaskEnum;
use app\common\model\draw\DrawTask;
use app\common\service\discord\DiscordConfig;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use think\facade\Log;

/**
 * Class MessageHandler
 * @package app\common\service\discord\event
 */
abstract class MessageHandler
{

    abstract protected function handle(Message $message, Discord $discord);

    /**
     * @notes 图片url
     * @param Message $message
     * @return mixed|string|null
     * @author mjf
     * @date 2023/8/2 10:31
     */
    protected function getImageUrl(Message $message)
    {
        $attachments = $message->attachments->toArray();
        $attachments = array_values($attachments);
        if (!empty($attachments)) {
            return $attachments[0]["url"] ?? '';
        }
        return null;
    }

    /**
     * @notes 图片hash
     * @param string $imageUrl
     * @return false|string
     * @author mjf
     * @date 2023/8/2 10:31
     */
    protected function getImageHash(string $imageUrl)
    {
        $hashStartIndex = strrpos($imageUrl, "_");
        $hash = substr($imageUrl, $hashStartIndex + 1);
        return strstr($hash, ".", true);
    }

    /**
     * @notes 是否为忽略信息
     * @param Message $message
     * @param Discord $discord
     * @return bool
     * @author mjf
     * @date 2023/8/2 11:07
     */
    protected function isIgnoreMessage(Message $message, Discord $discord)
    {
        // 过滤非指定频道信息
        $discordConfig = new DiscordConfig();
        if ($message->channel_id != $discordConfig->getChannelId()) {
            return true;
        }

        // 排除个人信息
        if ($message->user_id == $discord->user->id) {
            return true;
        }

        Log::record("discord-msg" . $message->author->username . "-" . $message->content);
        return false;
    }

    /**
     * @notes 任务成功
     * @param DrawTask $task
     * @param Message $message
     * @author mjf
     * @date 2023/8/2 18:46
     */
    protected function taskSuccess(DrawTask $task, Message $message)
    {
        $imageUrl = $this->getImageUrl($message);
        $imageUrl = $this->imageUrlTrim($imageUrl);

        $task = (new DrawTask())->findOrEmpty($task['id']);
        $task->image_url = $imageUrl;
        $task->msg_hash = $this->getImageHash($imageUrl);
        $task->msg_id = $message->id;
        $task->finish_time = time();
        $task->status = DrawTaskEnum::STATUS_SUCCESS;
        $task->progress = 100;
        $task->save();

        // 回调逻辑
        DrawLogic::notifyMdd([
            'code' => 1,
            'msg' => "成功",
            'data' => [
                'task_id' => $task->task_id,
                'action' => $task->action,
                'image_url' => $task->image_url,
            ],
        ]);
    }

    /**
     * @notes 任务失败
     * @param DrawTask $task
     * @param Message $message
     * @author mjf
     * @date 2023/8/2 18:46
     */
    protected function taskFail(DrawTask $task, Message $message)
    {
        $task = (new DrawTask())->findOrEmpty($task['id']);
        $task->status = DrawTaskEnum::STATUS_FAIL;
        $task->msg_id = $message->id;
        $task->save();
    }

    /**
     * @notes 根据关键词获取任务id
     * @param $prompt
     * @return mixed
     * @author mjf
     * @date 2023/8/8 15:59
     */
    protected function getTaskIdBPrompt($prompt)
    {
        // 匹配 <!id:> 标记及其中的数字部分
        $pattern = '/<!id:(\d+)>/';
        preg_match($pattern, $prompt, $matches);
        return !empty($matches[1]) ? $matches[1] : $prompt;
    }

    /**
     * @notes 图片地址过滤
     * @param $image
     * @return mixed|string
     * @author mjf
     * @date 2023/9/26 15:36
     */
    protected function imageUrlTrim($image)
    {
//        $check = strpos($image, '?ex=');
//        if ($check !== false) {
//            return mb_substr($image, 0, $check);
//        }
        return $image;
    }

}
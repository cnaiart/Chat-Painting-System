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

namespace app\common\service\discord;

use app\common\service\discord\event\ImagineHandler;
use app\common\service\discord\event\UpscaleHandler;
use app\common\service\discord\event\VariationHandler;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use think\facade\Log;

class EventHandler
{

    private function eventClass()
    {
        return [
            ImagineHandler::class,
            UpscaleHandler::class,
            VariationHandler::class
        ];
    }

    /**
     * @notes 数据处理
     * @param Message $message
     * @param Discord $discord
     * @author mjf
     * @date 2023/8/2 18:57
     */
    public function handle(Message $message, Discord $discord)
    {
        if (!empty($message->author->username) && !empty($message->content)) {
            Log::record("discord-imagineMsg" . "{$message->author->username}: {$message->content}");
        }

        $attachmentsTest = $message->attachments->toArray();
        if (!empty($attachmentsTest)) {
            Log::record("discord-attachments" . json_encode($attachmentsTest, JSON_UNESCAPED_UNICODE));
        }

        foreach ($this->eventClass() as $eventHandle) {
            $handler = app()->make($eventHandle);
            $handler->handle($message, $discord);
        }
    }

}
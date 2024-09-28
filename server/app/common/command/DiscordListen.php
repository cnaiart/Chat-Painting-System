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

namespace app\common\command;

use app\common\service\discord\DiscordConfig;
use app\common\service\discord\DiscordListenService;
use app\common\service\discord\EventHandler;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;
use Monolog\Logger;
use React\EventLoop\Loop;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Log;


class DiscordListen extends Command
{
    protected function configure()
    {
        $this->setName('discord')
            ->setDescription('discord服务');
    }

    protected function execute(Input $input, Output $output)
    {
        try {
            $discordConfig = new DiscordConfig();
            $botToken = $discordConfig->getBotToken();
            if (empty($botToken)) {
                throw new \Exception("token缺失");
            }

            $discord = new DiscordListenService([
                'token' => $botToken,
                'disabledEvents' => [
                    Event::MESSAGE_DELETE,
                    Event::INTERACTION_CREATE,
                ],
                'loop' => Loop::get(),
                'logger' => new Logger('discord-log'),
            ]);

            $discord->on('ready', function (Discord $discord) {
                echo "Bot is ready!", PHP_EOL;
                $discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) {
                    (new EventHandler())->handle($message, $discord);
                });
            });

            $discord->run();

        } catch (\Throwable $e) {
            echo $e->getLine(), PHP_EOL;
            echo $e->getFile(), PHP_EOL;
            echo $e->getMessage(), PHP_EOL;
            Log::record("discord-Listen-error" . $e->getMessage()
                . "file--" . $e->getFile() . "line--" . $e->getLine());
        }
    }


}
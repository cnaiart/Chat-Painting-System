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

use Discord\Discord;
use React\Promise\ExtendedPromiseInterface;

/**
 * discord服务
 * Class DiscordService
 * @package app\common\service\discord
 */
class DiscordListenService extends Discord
{

    public function __construct(array $options = [])
    {
        parent::__construct($options);
    }

    protected function connectWs(): void
    {
        $gateway = (new DiscordConfig())->getDiscordWss();
        $this->setGateway($gateway)->done(function ($gateway) {
            if (isset($gateway['session']) && $session = $gateway['session']) {
                if ($session['remaining'] < 2) {
                    $this->logger->error('exceeded number of reconnects allowed, waiting before attempting reconnect', $session);
                    $this->loop->addTimer($session['reset_after'] / 1000, function () {
                        $this->connectWs();
                    });
                    return;
                }
            }

            $this->logger->info('starting connection to websocket', ['gateway' => $this->gateway]);

            /** @var ExtendedPromiseInterface */
            $promise = ($this->wsFactory)($this->gateway);
            $promise->done(
                [$this, 'handleWsConnection'],
                [$this, 'handleWsConnectionFailed']
            );
        });
    }


}
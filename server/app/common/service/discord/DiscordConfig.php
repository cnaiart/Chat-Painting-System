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

use app\common\enum\DrawEnum;
use app\common\service\ConfigService;

/**
 * Class DiscordConfig
 * @package app\common\service\discord
 */
class DiscordConfig
{
    private array $drawConfig;

    // api请求地址
    private string $apiUrl = "https://discord.com";
    // wss链接地址
    private string $wssUrl = "wss://gateway.discord.gg";
    // discord-服务器id
    private string $guildId = "";
    // discord-频道id
    private string $channelId = "";
    // discord-session_id
    private string $sessionId = "";

    // discord-mj-app_id(固定参数)
    private string $appId = "936929561302675456";
    // discord-mj-version(固定参数)
    private string $version = "1166847114203123795";
    // discord-mj-data_id(固定参数)
    private string $dataId = "938956540159881230";

    // discord-bot-token
    private string $botToken = "";


    public function __construct()
    {
        // 初始化配置
        $this->drawConfig = ConfigService::get('draw_config', DrawEnum::API_MDDAI_MJ, []);
    }

    /**
     * @notes discord-api
     * @return string
     * @author mjf
     * @date 2023/9/1 10:12
     */
    public function getDiscordApi(): string
    {
        return !empty($this->drawConfig['proxy_api'])
            ? $this->drawConfig['proxy_api']
            : $this->apiUrl;
    }

    /**
     * @notes discord-wss
     * @return string
     * @author mjf
     * @date 2023/9/1 10:12
     */
    public function getDiscordWss(): string
    {
        return !empty($this->drawConfig['proxy_wss'])
            ? $this->drawConfig['proxy_wss']
            : $this->wssUrl;
    }

    /**
     * @notes discord-频道id
     * @return string
     * @author mjf
     * @date 2023/10/8 15:25
     */
    public function getChannelId(): string
    {
        return !empty($this->drawConfig['channel_id'])
            ? $this->drawConfig['channel_id']
            : $this->channelId;
    }

    /**
     * @notes discord-服务器id
     * @return string
     * @author mjf
     * @date 2023/10/8 15:26
     */
    public function getGuildId(): string
    {
        return !empty($this->drawConfig['guild_id'])
            ? $this->drawConfig['guild_id']
            : $this->guildId;
    }

    /**
     * @notes discord-session_id
     * @return string
     * @author mjf
     * @date 2023/10/8 15:28
     */
    public function getSessionId(): string
    {
        return !empty($this->drawConfig['session_id'])
            ? $this->drawConfig['session_id']
            : $this->sessionId;
    }

    /**
     * @notes discord-bot-token
     * @return string
     * @author mjf
     * @date 2023/10/9 10:09
     */
    public function getBotToken(): string
    {
        return !empty($this->drawConfig['bot_token'])
            ? $this->drawConfig['bot_token']
            : $this->botToken;
    }

    /**
     * @notes discord-appid
     * @return string
     * @author mjf
     * @date 2023/10/8 18:43
     */
    public function getAppId(): string
    {
        return $this->appId;
    }

    /**
     * @notes discord-version
     * @return string
     * @author mjf
     * @date 2023/10/8 18:43
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @notes discord-dataId
     * @return string
     * @author mjf
     * @date 2023/10/8 18:43
     */
    public function getDataId(): string
    {
        return $this->dataId;
    }

}
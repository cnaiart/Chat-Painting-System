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

use WpOrg\Requests\Requests;

/**
 * Class DiscordSubmitService
 * @package app\common\service\discord
 */
class DiscordSubmitService
{
    private string $userToken;

    public function __construct($userToken)
    {
        $this->userToken = $userToken;
    }

    /**
     * @notes 提交绘图
     * @param $prompt
     * @return mixed
     * @throws \Exception
     * @author mjf
     * @date 2023/6/6 15:02
     */
    public function imagine($prompt)
    {
        $discordConfig = new DiscordConfig();
        $requestParams = [
            'type' => 2,
            "guild_id" => $discordConfig->getGuildId(),
            "channel_id" => $discordConfig->getChannelId(),
            "application_id" => $discordConfig->getAppId(),
            "session_id" => $discordConfig->getSessionId(),
            "data" => [
                "version" => $discordConfig->getVersion(),
                "id" => $discordConfig->getDataId(),
                "name" => "imagine",
                "type" => 1,
                "options" => [
                    [
                        "type" => 3,
                        "name" => "prompt",
                        "value" => $prompt
                    ]
                ],
                'application_command' => [
                    'id' => $discordConfig->getDataId(),
                    'application_id' => $discordConfig->getAppId(),
                    'version' => $discordConfig->getVersion(),
                    'default_permission' => true,
                    'default_member_permissions' => '',
                    'type' => 1,
                    'nsfw' => false,
                    'name' => 'imagine',
                    'description' => 'Create images with Midjourney',
                    'dm_permission' => true,
                    'options' => [
                        [
                            'type' => 3,
                            'name' => 'prompt',
                            'description' => 'The prompt to imagine',
                            'required' => true
                        ]
                    ]
                ],
                'attachments' => []
            ]
        ];
        $uri = "/interactions";
        return $this->postRequest($uri, $requestParams);
    }

    /**
     * @notes 绘图变化
     * @param string $msgId
     * @param int $index
     * @param string $msgHash
     * @return mixed
     * @throws \Exception
     * @author mjf
     * @date 2023/8/1 16:52
     */
    public function upscale(string $msgId, int $index, string $msgHash)
    {
        $discordConfig = new DiscordConfig();
        $requestParams = [
            'type' => 3,
            "message_id" => $msgId,
            "guild_id" => $discordConfig->getGuildId(),
            "channel_id" => $discordConfig->getChannelId(),
            "application_id" => $discordConfig->getAppId(),
            "session_id" => $discordConfig->getSessionId(),
            "message_flags" => 0,
            "data" => [
                "component_type" => 2,
                "custom_id" => "MJ::JOB::upsample::" . $index . "::" . $msgHash
            ]
        ];
        $uri = "/interactions";
        return $this->postRequest($uri, $requestParams);
    }

    /**
     * @notes 绘图变化
     * @param string $msgId
     * @param int $index
     * @param string $msgHash
     * @return mixed
     * @throws \Exception
     * @author mjf
     * @date 2023/8/1 17:39
     */
    public function variate(string $msgId, int $index, string $msgHash)
    {
        $discordConfig = new DiscordConfig();
        $requestParams = [
            'type' => 3,
            "message_id" => $msgId,
            "guild_id" => $discordConfig->getGuildId(),
            "channel_id" => $discordConfig->getChannelId(),
            "application_id" => $discordConfig->getAppId(),
            "session_id" => $discordConfig->getSessionId(),
            "message_flags" => 0,
            "data" => [
                "component_type" => 2,
                "custom_id" => "MJ::JOB::variation::" . $index . "::" . $msgHash
            ]
        ];
        $uri = "/interactions";
        return $this->postRequest($uri, $requestParams);
    }

    /**
     * @notes post请求
     * @param $params
     * @return mixed
     * @throws \Exception
     * @author mjf
     * @date 2023/6/6 14:58
     */
    public function postRequest($uri, $params)
    {
        $url = (new DiscordConfig())->getDiscordApi() . '/api/v9' . $uri;
        $headers = [
            "Authorization" => $this->userToken,
            "Content-Type" => "application/json;charset=UTF-8",
            "User-Agent" => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/112.0.0.0 Safari/537.36",
        ];
        $response = Requests::post($url, $headers, json_encode($params));
        $response = json_decode($response->body, true);
        if (!empty($response['message'])) {
            throw new \Exception($response['message']);
        }
        return $response;
    }

    /**
     * @notes 搜索
     * @param $taskId
     * @return mixed
     * @throws \Exception
     * @author mjf
     * @date 2023/9/26 11:39
     */
    public function search($taskId)
    {
        $uri = '/guilds/' . (new DiscordConfig())->getGuildId() . '/messages/search?content=' . $taskId;
        return $this->getRequest($uri);
    }

    /**
     * @notes get请求
     * @param $uri
     * @return mixed
     * @throws \Exception
     * @author mjf
     * @date 2023/9/26 11:39
     */
    public function getRequest($uri)
    {
        $url = (new DiscordConfig())->getDiscordApi() . '/api/v9'. $uri;
        $headers = [
            "Authorization" => $this->userToken,
            "Content-Type" => "application/json;charset=UTF-8",
            "User-Agent" => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/112.0.0.0 Safari/537.36",
        ];
        $response = Requests::get($url, $headers);
        return json_decode($response->body, true);
    }

}
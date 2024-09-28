<?php
// +----------------------------------------------------------------------
// | likeshop100%开源免费商用商城系统
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | 商业版本务必购买商业授权，以免引起法律纠纷
// | 禁止对系统程序代码以任何目的，任何形式的再发布
// | gitee下载：https://gitee.com/likeshop_gitee
// | github下载：https://github.com/likeshop-github
// | 访问官网：https://www.likeshop.cn
// | 访问社区：https://home.likeshop.cn
// | 访问手册：http://doc.likeshop.cn
// | 微信公众号：likeshop技术社区
// | likeshop团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeshopTeam
// +----------------------------------------------------------------------
namespace app\adminapi\logic\setting;

use app\common\enum\voice\VoiceEnum;
use app\common\service\ConfigService;

/**
 * 语音控制器配置类
 * Class VoiceSettingLogic
 * Package app\adminapi\logic\setting
 */
class VoiceSettingLogic{

    /**
     * @notes 获取语音播报配置
     * @return array
     * @author cjhao
     * @date 2023/10/9 17:00
     */
    public function getConfig()
    {
        //渠道配置
        $voiceChannelConfig = VoiceEnum::getVoiceChannelDefaultConfig();
        $voiceInputChannelConfig = VoiceEnum::getVoiceInputChannelDefaultConfig();
        $voiceBroadcastChannel  = [];
        $voiceInputChannel      = [];
        $voiceChatChannel       = [];
        foreach ($voiceChannelConfig as $key => $defaultConfig)
        {
            //语音播报通道配置
            $config = ConfigService::get('voice_config',$key,[]);
            $config = array_merge($defaultConfig,$config);
            if(VoiceEnum::KDXF == $key){
                $config['speed'] = (int)$config['speed'];
            }else{
                $config['speed'] = (float)$config['speed'];
            }
            $voiceBroadcastChannel[$key] = $config;
            //语音对话通道配置
            $config = ConfigService::get('voice_chat_config',$key,[]);
            $config = array_merge($defaultConfig,$config);
            if(VoiceEnum::KDXF == $key){
                $config['speed'] = (int)$config['speed'];
            }else{
                $config['speed'] = (float)$config['speed'];
            }
            $voiceChatChannel[$key] = $config;
        }
        foreach ($voiceInputChannelConfig as $key => $defaultConfig)
        {
            //语音输入通道配置
            $config = ConfigService::get('voice_input_config',$key,[]);
            $config = array_merge($defaultConfig,$config);
            $voiceInputChannel[$key] = $config;
        }
        return [
            'voice_broadcast'   => [
                'is_open'       => ConfigService::get('voice_config','is_open'),
                'channel'       => ConfigService::get('voice_config','channel'),
                'channel_config'=> $voiceBroadcastChannel,
            ],
            'voice_input'   => [
                'is_open'       => ConfigService::get('voice_input_config','is_open'),
                'channel'       => ConfigService::get('voice_input_config','channel'),
                'channel_config'=> $voiceInputChannel,
            ],
            'voice_chat'   => [
                'is_open'       => ConfigService::get('voice_chat_config','is_open'),
                'channel'       => ConfigService::get('voice_chat_config','channel'),
                'channel_config'=> $voiceChatChannel,
            ],

        ];
    }


    /**
     * @notes 设置语音播报
     * @param array $post
     * @return void
     * @author cjhao
     * @date 2023/10/9 17:01
     */
    public function setConfig(array $post)
    {
        $broadcastchannel = $post['voice_broadcast']['channel_config'][$post['voice_broadcast']['channel']];
        $inputchannel = $post['voice_input']['channel_config'][$post['voice_input']['channel']] ?? [];
        $chatchannel = $post['voice_chat']['channel_config'][$post['voice_chat']['channel']];
        //语音播报
        ConfigService::set('voice_config','is_open',$post['voice_broadcast']['is_open']);
        ConfigService::set('voice_config','channel',$post['voice_broadcast']['channel']);
        ConfigService::set('voice_config',$post['voice_broadcast']['channel'],$broadcastchannel);
        //语音输入
        ConfigService::set('voice_input_config','is_open',$post['voice_input']['is_open']);
        ConfigService::set('voice_input_config','channel',$post['voice_input']['channel']);
        ConfigService::set('voice_input_config',$post['voice_input']['channel'],$inputchannel);
        //语音对话
        ConfigService::set('voice_chat_config','is_open',$post['voice_chat']['is_open']);
        ConfigService::set('voice_chat_config','channel',$post['voice_chat']['channel']);
        ConfigService::set('voice_chat_config',$post['voice_chat']['channel'],$chatchannel);
    }

}
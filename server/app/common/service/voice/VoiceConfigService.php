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
namespace app\common\service\voice;
use app\common\enum\voice\VoiceEnum;
use app\common\service\ConfigService;

/**
 * 语音配置类
 * Class VoiceConfigService
 * @package app\common\service\voice
 */
class VoiceConfigService
{


    /**
     * @notes 获取语音配置
     * @param int $type
     * @return array
     * @author cjhao
     * @date 2024/2/1 12:09
     */
    public static function getVoiceConfig(int $type)
    {

        $voiceConfig  = [];
        switch ($type){
            case VoiceEnum::VOICE_BROADCAST:
                $channel = ConfigService::get('voice_config','channel');
                $voiceConfig = [
                    'is_open'       => ConfigService::get('voice_config','is_open'),
                    'channel'       => ConfigService::get('voice_config','channel'),
                    'channel_config'=> ConfigService::get('voice_config',$channel),
                ];
                break;
            case VoiceEnum::VOICE_INPUT:
                $voiceConfig = [
                    'is_open'       => ConfigService::get('voice_input_config','is_open'),
                    'channel'       => ConfigService::get('voice_input_config','channel'),
                    'channel_config'=> [],
                ];
                break;
            case VoiceEnum::VOICE_CHAT:
                $channel = ConfigService::get('voice_chat_config','channel');
                $voiceConfig = [
                    'is_open'       => ConfigService::get('voice_chat_config','is_open'),
                    'channel'       => $channel,
                    'channel_config'=> ConfigService::get('voice_chat_config',$channel),
                ];
                break;
        }
        if($voiceConfig && empty($voiceConfig['channel_config'])){
            $voiceConfig['channel_config'] = VoiceEnum::getChannelDefaultConfig($voiceConfig['channel']);
        }
        return $voiceConfig;
    }
}
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
namespace app\common\enum\voice;

/**
 * 语音播报枚举类
 * Class VoiceEnum
 * Package app\common\enum
 */
class VoiceEnum
{
    const VOICE_BROADCAST   = 1;
    const VOICE_INPUT       = 2;
    const VOICE_CHAT        = 3;

    const KDXF   = 'kdxf';

    const OPENAI    = 'openai';

    /**
     * @notes 获取发音人渠道
     * @param $form
     * @return string|array
     * @author cjhao
     * @date 2023/10/13 9:46
     */
    public static function getKdxfPronounceList($form = true)
    {
        $desc = [
            'xiaoyan'           => '讯飞小燕',
            'aisjiuxu'          => '讯飞许久',
            'aisxping'          => '讯飞小萍',
            'aisjinger'         => '讯飞小婧',
            'aisbabyxu'         => '讯飞许小宝',
        ];
        if(true === $form){
            return $desc;
        }
        return $desc[$form] ?? '';
    }

    /**
     * @notes 获取发音人渠道
     * @param $form
     * @return string|string[]
     * @author cjhao
     * @date 2023/10/13 9:46
     */
    public static function getOpenAiPronounceList($form = true){
        $desc = [
            'alloy'             => 'alloy',
            'echo'              => 'echo',
            'fable'             => 'fable',
            'onyx'              => 'onyx',
            'nova'              => 'nova',
            'shimmer'           => 'shimmer',
        ];
        if(true === $form){
            return $desc;
        }
        return $desc[$form] ?? '';
    }
    /**
     * @notes 获取通道
     * @param $form
     * @return string|string[]
     * @author cjhao
     * @date 2023/10/9 16:59
     */
    public static function getChannel($form = true)
    {
        $desc = [
            self::KDXF      => '科大讯飞',
            self::OPENAI    => 'openAi-TTS'
        ];
        if(true === $form){
            return $desc;
        }
        return $desc[$form] ?? '';

    }

    /**
     * @notes 获取语音播报，语音对话
     * @param $form
     * @return array|array[]
     * @author cjhao
     * @date 2024/2/20 12:17
     */
    public static function getVoiceChannelDefaultConfig($form = true){
        $desc = [
            self::KDXF  => [
                'name'              => '科大讯飞',
                'pronounce_list'    => self::getKdxfPronounceList(),
                'pronounce'         => 'xiaoyan',
                'speed'             => 50,
            ],
            self::OPENAI    => [
                'name'              => 'openAi-TTS',
                'pronounce_list'    => self::getOpenAiPronounceList(),
                'pronounce'         => 'alloy',
                'speed'             => 1.0,
                'model_list'        => [
                    'tts-1'     =>  'tts-1',
                    'tts-1-hd'  =>  'tts-1-hd'
                ],
                'model'         => 'tts-1',
                'agency_api'    => '',

            ],

        ];
        if(true === $form){
            return $desc;
        }
        return $desc[$form] ?? [];
    }

    /**
     * @notes 获取语音输入渠道配置
     * @param $form
     * @return array|array[]
     * @author cjhao
     * @date 2023/10/13 10:04
     */
    public static function getVoiceInputChannelDefaultConfig($form = true)
    {
        $desc = [
            self::KDXF  => [
                'name'              => '科大讯飞',
                'pronounce_list'    => self::getKdxfPronounceList(),
                'pronounce'         => 'xiaoyan',
                'speed'             => 50,
            ],
            self::OPENAI    => [
                'name'              => 'openAi-TTS',
                'model_list'        => [
                    'whisper-1'     =>  'whisper-1',
                ],
                'model'         => 'whisper-1',
                'agency_api'    => '',

            ],

        ];
        if(true === $form){
            return $desc;
        }
        return $desc[$form] ?? [];
    }


    /**
     * @notes 获取语音类型
     * @param $form
     * @return array|string|string[]
     * @author cjhao
     * @date 2024/1/26 10:55
     */
    public static function getVoiceTypeLists($form = true)
    {
        $desc = [
            self::VOICE_BROADCAST   => '语音播报',
            self::VOICE_INPUT       => '语音输入',
            self::VOICE_CHAT        => '语音对话',

        ];
        if(true === $form){
            return $desc;
        }
        return $desc[$form] ?? [];

    }
}

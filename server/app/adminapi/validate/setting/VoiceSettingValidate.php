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
namespace app\adminapi\validate\setting;
use app\common\enum\voice\VoiceEnum;
use app\common\validate\BaseValidate;

/**
 * 语音配置逻辑类
 * Class VoiceSettingValidate
 * @package app\adminapi\validate\setting
 */
class VoiceSettingValidate extends BaseValidate
{
    protected $rule = [
        'voice_broadcast'   => 'require|array|checkBroadcase',
        'voice_chat'        => 'require|array|checkChat',
        'voice_input'       => 'require|array|checkInput',
    ];
    protected $message = [
        'voice_broadcast.require'   => '语音播报数据缺少',
        'voice_chat.require'        => '语音对话数据缺少',
        'voice_input.require'       => '语音输入数据缺少',
        'voice_broadcast.array'     => '语音输入数据格式错误',
        'voice_chat.array'          => '语音输入数据格式错误',
        'voice_input.array'         => '语音输入数据格式错误',
    ];

    /**
     * @notes 验证语音播报配置
     * @param $value
     * @param $rule
     * @param $data
     * @return string|true
     * @author cjhao
     * @date 2024/1/26 16:45
     */
    protected function checkBroadcase($value, $rule, $data)
    {
        $isOpen = $value['is_open'] ?? '';
        $channel = $value['channel'] ?? '';
        $channelConfig = $value['channel_config'] ?? '';
        if('' == $isOpen){
            return '请选择语音播报状态';
        }
        if('' == $channel){
            return '请选择语音播报通道';
        }
        if('' == $channelConfig){
            return '请设置语音播报通道配置';
        }
        return true;

    }

    /**
     * @notes 验证语音对话配置
     * @param $value
     * @param $rule
     * @param $data
     * @return string|true
     * @author cjhao
     * @date 2024/1/26 16:45
     */
    protected function checkChat($value, $rule, $data)
    {
        $isOpen = $value['is_open'] ?? '';
        $channel = $value['channel'] ?? '';
        $channelConfig = $value['channel_config'] ?? '';
        if('' == $isOpen){
            return '请选择语音对话状态';
        }
        if('' == $channel){
            return '请选择语音对话通道';
        }
        if('' == $channelConfig){
            return '请设置语音对话通道配置';
        }
        return true;
    }

    /**
     * @notes 验证语音输入配置
     * @param $value
     * @param $rule
     * @param $data
     * @return string|true
     * @author cjhao
     * @date 2024/1/26 16:45
     */
    protected function checkInput($value, $rule, $data)
    {
        $isOpen = $value['is_open'] ?? '';
        $channel = $value['channel'] ?? '';
        $channelConfig = $value['channel_config'] ?? '';
        if('' == $isOpen){
            return '请选择语音输入状态';
        }
        if('' == $channel){
            return '请选择语音输入通道';
        }
//        if('' == $channelConfig){
//            return '请设置语音输入通道配置';
//        }
        return true;
    }
}
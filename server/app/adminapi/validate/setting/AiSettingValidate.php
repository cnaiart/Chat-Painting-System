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
use app\common\enum\chat\ChatEnum;
use app\common\validate\BaseValidate;

/**
 * ai参数配置验证类
 * Class AiSettingValidate
 * @package app\adminapi\validate\setting
 */
class AiSettingValidate extends BaseValidate
{

    protected $rule = [
        'is_markdown'           => 'require|in:0,1',
        'chat_logo'             => 'require',
        'chat_limit_tips'       => 'require',
        'default_reply_open'    => 'require|in:0,1',
        'default_reply'         => 'requireIf:default_reply_open,1',
        'is_reopen'             => 'require|in:0,1',
    ];

    protected $message = [
        'is_sensitive.in'               => '敏感词库值错误',
        'is_markdown.require'           => '请选择是否开启markdown渲染',
        'is_markdown.in'                => 'markdown渲染值错误',
        'chat_logo.require'             => '请选择对话图标',
        'chat_limit_tips.require'       => '请输入对话上限提示语',
        'default_reply_open.require'    => '请选择对话默认回复的状态',
        'default_reply_open.in'         => '请选择对话默认回复的状态错误',
        'default_reply.requireIf'       => '请输入对话默认回复内容',
        'is_reopen.require'             => '请选择重开对话状态',
        'is_reopen.in'                  => '重开对话状态错误',
    ];

    public function sceneSetChatConfig()
    {
        return $this->only(['is_markdown','chat_logo','chat_limit_tips','default_reply_open','default_reply','is_reopen']);
    }
}
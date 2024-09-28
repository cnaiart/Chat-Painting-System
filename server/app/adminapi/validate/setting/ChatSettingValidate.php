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
 * 对话配置验证器类
 * Class ChatSettingValidate
 * @package app\adminapi\validate\setting
 */
class ChatSettingValidate extends BaseValidate
{
    protected $rule = [
        'key'                   => 'require',
        'model'                 => 'checkModel',
        'temperature'           => 'require|between:0,1',
        'context_num'           => 'require|in:0,1,2,3,4,5',
//        'top_p'               => 'require|between:0,1',
//        'presence_penalty'    => 'require|between:0,1',
//        'frequency_penalty'   => 'require|between:0,1',
//        'n'                   => 'require|in:1,2,3,4,5',
    ];

    protected $message = [
        'key.require'                       => '请选择语言模型',
        'model.require'                     => '请选择模型',
        'temperature.require'               => '请选择词汇属性',
        'temperature.between'               => '词汇属性值在0~1之间',
        'context_num.require'               => '请选择上下文总数',
        'context_num.in'                    => '上下文总数值错误',
        'top_p.require'                     => '请选择随机属性',
        'top_p.between'                     => '随机属性值在0~1之间',
        'presence_penalty.require'          => '请选择话题属性',
        'presence_penalty.between'          => '题属性值在0~1之间',
        'frequency_penalty.require'         => '请选择重复属性',
        'frequency_penalty.between'         => '重复属性值在0~1之间',
        'n.require'                         => '请选择最大回复',
        'n.in'                              => '最大回复值错误',
    ];

    /**
     * @notes 模型验证
     * @param $value
     * @param $rule
     * @param $data
     * @return string|true
     * @author cjhao
     * @date 2023/12/25 15:17
     */
    public function checkModel($value,$rule,$data)
    {
        $chatLists = ChatEnum::getChatModelLists();
        $chatLists = array_keys($chatLists);
        if(!in_array($value,$chatLists)){
            return '模型不可用，请重新选择';
        }
        return true;
    }




}
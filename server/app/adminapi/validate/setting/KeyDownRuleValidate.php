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
use app\common\enum\DrawEnum;
use app\common\enum\KeyPoolEnum;
use app\common\validate\BaseValidate;


class KeyDownRuleValidate extends BaseValidate
{
    protected $rule = [
        'id'        => 'require',
        'type'      => 'require|in:'.KeyPoolEnum::TYPE_CHAT.','.KeyPoolEnum::TYPE_DRAW,
        'ai_key'    => 'require|checkAiKey',
        'rule'       => 'require',
        'prompt'       => 'require',
        'status'    => 'require|in:0,1',
    ];
    protected $message = [
        'id.require'      => '参数缺失',
        'type.require'    => '规则类型缺失',
        'type.in'         => '规则类型错误',
        'ai_key.require'  => '请选择接口类型',
        'rule.require'     => '请输入停用规则',
        'prompt.require'     => '请输入停用提示',
        'status.require'  => '请选择状态',
        'status.in'       => '状态错误',
     ];

    public function sceneAdd()
    {
        return $this->remove('id',true);
    }

    public function sceneId()
    {
        return $this->only(['id']);
    }

    /**
     * @notes 验证ai_key
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author cjhao
     * @date 2023/7/25 15:05
     */
    public function checkAiKey($value,$rule,$data)
    {
        if(KeyPoolEnum::TYPE_DRAW == $data['type']){
            $aiModelName = DrawEnum::getAiModelName($value);
        }else{
            $aiModelName = ChatEnum::getChatName($value);
        }
        if(empty($aiModelName)){
            return '模型错误，请重新选择';
        }
        return true;
    }

}
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
namespace app\adminapi\validate\cardcode;
use app\common\enum\CardCodeEnum;
use app\common\model\cardcode\CardCode;
use app\common\model\member\MemberPackage;
use app\common\model\recharge\RechargePackage;
use app\common\validate\BaseValidate;

/**
 * 卡密验证器类
 * Class CardCodeController
 * @package app\adminapi\validate\cardecode
 */
class CardCodeValidate extends BaseValidate
{

    protected $rule = [
        'id'                => 'require',
        'type'              => 'require|checkType',
        'relation_id'       => 'requireIf:type,1,2',
        'card_num'          => 'require|gt:0|lt:500',
        'valid_start_time'  => 'require|gt:0',
        'valid_end_time'    => 'require|gt:0',

    ];

    protected $message = [
        'id.require'                => '请选择卡密',
        'type.require'              => '请选择卡密类型',
        'type.in'                   => '卡密类型错误',
        'relation_id.requireIf'     => '请选择卡密',
        'card_num.require'          => '请输入卡密数量',
        'card_num.gt'               => '卡密数量不能小于0',
        'card_num.lt'               => '卡密数量不能大于500',
        'valid_start_time.require'  => '请选择失效时间',
        'valid_start_time.lt'       => '生效时间错误',
        'valid_end_time.require'    => '请选择生效时间',
        'valid_end_time.lt'         => '生效时间错误',
    ];

    protected function sceneAdd()
    {
        return $this->remove(['id'=>true]);
    }

    protected function sceneId()
    {
        return $this->only(['id']);
    }


    protected function checkType($value,$rule,$data)
    {
        if(!in_array($value,[CardCodeEnum::TYPE_MEMBER,CardCodeEnum::TYPE_RECHARGE,CardCodeEnum::TYPE_CHAT_NUM,CardCodeEnum::TYPE_DRAW_NUM])){
            return '类型错误';
        }
        //验证会员套餐
        if(CardCodeEnum::TYPE_MEMBER == $value){
            $package = MemberPackage::where(['id' => $data['relation_id']])->findOrEmpty();
            if($package->isEmpty()){
                return '套餐错误，请重新选择';
            }
        }
        //验证充值套餐
        if(CardCodeEnum::TYPE_RECHARGE == $value){
            $package = RechargePackage::where(['id' => $data['relation_id']])->findOrEmpty();
            if($package->isEmpty()){
                return '套餐错误，请重新选择';
            }
        }
        return true;

    }


}
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

namespace app\api\validate;

use app\common\enum\PayEnum;
use app\common\model\recharge\RechargePackage;
use app\common\service\ConfigService;
use app\common\validate\BaseValidate;

/**
 * 用户验证器
 * Class UserValidate
 * @package app\shopapi\validate
 */
class RechargeValidate extends BaseValidate
{

    protected $rule = [
        'recharge_package_id' => 'require|checkRechargePackageId',
    ];


    protected $message = [
        'recharge_package_id.require' => '请选择充值套餐',
    ];


    public function sceneRecharge()
    {
        return $this->only(['recharge_package_id']);
    }


    /**
     * @notes 校验充值套餐
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2023/4/19 2:31 下午
     */
    protected function checkRechargePackageId($value, $rule, $data)
    {
        $result = RechargePackage::where('id',$value)->findOrEmpty();
        if ($result->isEmpty()) {
            return '充值套餐错误，请重新选择';
        }
        if ($result->status != 1) {
            return '充值套餐已下架，请重新选择';
        }

        return true;
    }

}
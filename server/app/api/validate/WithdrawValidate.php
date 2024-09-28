<?php
// +----------------------------------------------------------------------
// | likeshop开源商城系统
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | gitee下载：https://gitee.com/likeshop_gitee
// | github下载：https://github.com/likeshop-github
// | 访问官网：https://www.likeshop.cn
// | 访问社区：https://home.likeshop.cn
// | 访问手册：http://doc.likeshop.cn
// | 微信公众号：likeshop技术社区
// | likeshop系列产品在gitee、github等公开渠道开源版本可免费商用，未经许可不能去除前后端官方版权标识
// |  likeshop系列产品收费版本务必购买商业授权，购买去版权授权后，方可去除前后端官方版权标识
// | 禁止对系统程序代码以任何目的，任何形式的再发布
// | likeshop团队版权所有并拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeshop.cn.team
// +----------------------------------------------------------------------

namespace app\api\validate;


use app\common\model\user\User;
use app\common\service\ConfigService;
use app\common\validate\BaseValidate;

class WithdrawValidate extends BaseValidate
{
    protected $rule = [
        'money' => 'require|float|gt:0|checkMoney',
        'type' => 'require|in:1,2,3,4',
        'account' => 'requireIf:type,1|requireIf:type,3|requireIf:type,4',
        'real_name' => 'requireIf:type,1|requireIf:type,3|requireIf:type,4',
        'money_qr_code' => 'requireIf:type,3|requireIf:type,4',
        'id' => 'require',
    ];

    protected $message = [
        'money.require' => '请输入提现金额',
        'money.float' => '提现金额错误',
        'money.gt' => '提现金额必须大于0',
        'type.require' => '请选择提现方式',
        'type.in' => '提现方式值错误',
        'account.requireIf' => '请输入账号',
        'real_name.requireIf' => '请输入真实姓名',
        'money_qr_code.requireIf' => '请选择收款码',
        'id.require' => '参数缺失',
    ];


    public function sceneApply()
    {
        return $this->only(['money','type','account','real_name','money_qr_code']);
    }

    public function sceneDetail()
    {
        return $this->only(['id']);
    }

    /**
     * @notes 校验提现金额
     * @param $value
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2023/5/24 8:29 下午
     */
    public function checkMoney($value,$rule,$data)
    {
        $user = User::where('id',$data['user_id'])->findOrEmpty()->toArray();
        if ($user['distribution_status'] == 0) {
            return '分销已被冻结，无法提现';
        }

        $user['user_money'] = is_null($user['user_money']) ? 0 : $user['user_money'];
        if ($value > $user['user_money']){
            return '可提现佣金不足';
        }

        // 最低提现金额
        $min_withdraw = ConfigService::get('withdraw', 'min_money');
        if(!empty($min_withdraw) && $value < $min_withdraw){
            return '最低提现'.$min_withdraw.'元';
        }

        // 最高提现金额
        $max_withdraw = ConfigService::get('withdraw', 'max_money');
        if (!empty($max_withdraw) && $value > $max_withdraw){
            return '最高提现'.$max_withdraw.'元';
        }

        return true;
    }
}
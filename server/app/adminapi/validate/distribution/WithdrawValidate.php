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

namespace app\adminapi\validate\distribution;


use app\common\enum\WithdrawEnum;
use app\common\model\distribution\WithdrawApply;
use app\common\validate\BaseValidate;

class WithdrawValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require',
        'verify_status' => 'require|in:2,3',
        'verify_remark' => 'requireIf:verify_status,3',
        'transfer_status' => 'require|in:1,2',
        'transfer_remark' => 'requireIf:transfer_status,2',

        'open' => 'require|in:0,1',
        'type' => 'array',
        'min_money' => 'float|egt:1|lt:max_money',
        'max_money' => 'float|egt:1',
        'handling_fee' => 'float|egt:0|lt:100',
        'wechat_way' => 'requireIf:type,2|in:1,2',
    ];

    protected $message = [
        'id.require' => '参数缺失',
        'verify_status.require' => '请选择审核结果',
        'verify_status.in' => '审核结果值错误',
        'verify_remark.requireIf' => '请填写审核备注',
        'transfer_status.require' => '请选择转账结果',
        'transfer_status.in' => '转账结果值错误',
        'transfer_remark.requireIf' => '请填写拒绝原因',

        'open.require' => '请选择是否显示',
        'open.in' => '是否显示值错误',
//        'type.require' => '请选择提现方式',
        'type.array' => '提现方式数据结构错误',
        'min_money.float' => '最低提现金额值错误',
        'min_money.egt' => '最低提现金额必须大于等于1',
        'min_money.lt' => '最低提现金额不能大于最高提现金额',
        'max_money.float' => '最高提现金额值错误',
        'max_money.egt' => '最高提现金额必须大于等于1',
        'handling_fee.float' => '提现手续费值错误',
        'handling_fee.egt' => '提现手续费必须大于等于1',
        'handling_fee.lt' => '提现手续费不能大于100',
        'wechat_way.requireIf' => '请选择微信零钱接口',
        'wechat_way.in' => '微信零钱接口值错误',
    ];


    public function sceneVerify()
    {
        return $this->only(['id','verify_status','verify_remark'])
            ->append('id','checkVerify');
    }

    public function sceneTransfer()
    {
        return $this->only(['id','transfer_status','transfer_remark'])
            ->append('id','checkTransfer');
    }

    public function sceneDetail()
    {
        return $this->only(['id']);
    }

    public function sceneSetConfig()
    {
        return $this->only(['open','type','min_money','max_money','handling_fee','wechat_way']);
    }

    public function sceneSearch()
    {
        return $this->only(['id'])
            ->append('id','checkSearch');
    }


    /**
     * @notes 校验审核
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2023/5/24 2:57 下午
     */
    public function checkVerify($value,$rule,$data)
    {
        $result = WithdrawApply::where('id',$value)->findOrEmpty()->toArray();
        if ($result['verify_status'] != WithdrawEnum::VERIFY_STATUS_WAIT) {
            return '提现申请已审核，不能重复审核';
        }
        return true;
    }

    /**
     * @notes 校验转账
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2023/5/24 3:29 下午
     */
    public function checkTransfer($value,$rule,$data)
    {
        $result = WithdrawApply::where('id',$value)->findOrEmpty()->toArray();
        if ($result['status'] != WithdrawEnum::STATUS_ING) {
            return '非提现中的订单，无法转账操作';
        }
        return true;
    }

    /**
     * @notes 校验查询
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2023/6/20 5:12 下午
     */
    public function checkSearch($value,$rule,$data)
    {
        $withdrawApply = WithdrawApply::findOrEmpty($value);
        if($withdrawApply->status != WithdrawEnum::STATUS_ING) {
            return '非提现中状态无法查询结果';
        }
        if($withdrawApply->type != WithdrawEnum::TYPE_WECHAT) {
            return '非微信零钱提现方式无法查询结果';
        }
        return true;
    }
}
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

namespace app\adminapi\validate\member;


use app\common\enum\CardCodeEnum;
use app\common\enum\PayEnum;
use app\common\model\cardcode\CardCode;
use app\common\model\member\MemberPackage;
use app\common\model\user\UserMember;
use app\common\validate\BaseValidate;
use think\facade\Validate;

class MemberPackageValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require',
        'name' => 'require|unique:'.MemberPackage::class.',name',
        'is_perpetual' => 'require|in:0,1',
        'duration' => 'requireIf:is_perpetual,0|number',
        'duration_type' => 'requireIf:is_perpetual,0|in:1,2',
        'sell_price' => 'require|float|egt:0',
        'lineation_price' => 'float|egt:0',
        'is_retrieve' => 'require|in:0,1|checkRetrieve',
//        'retrieve_amount' => 'requireIf:is_retrieve,1|float|egt:0|elt:sell_price',
        'sort' => 'number',
        'status' => 'require|in:0,1',
        'is_default' => 'require|in:0,1',
        'chat_limit' => 'number|gt:0',
        'benefits_ids' => 'array',
        'give_draw_number' => 'number',
        'give_chat_number' => 'number',
        'member_time_view' => 'array',
        'is_quota' => 'require|in:0,1',
        'quota_value' => 'number|gt:0',
    ];

    protected $message = [
        'id.require' => '参数缺失',
        'name.require' => '请输入套餐名称',
        'name.unique' => '套餐名称已存在',
        'duration.requireIf' => '请输入套餐时长',
        'duration.number' => '套餐时长值错误',
        'duration_type.requireIf' => '请选择套餐时长类型',
        'duration_type.in' => '套餐时长类型值错误',
        'sell_price.require' => '请输入实际售价',
        'sell_price.float' => '实际售价值错误',
        'sell_price.egt' => '实际售价必须大于等于0',
        'lineation_price.float' => '划线价值错误',
        'lineation_price.egt' => '划线价必须大于等于0',
        'is_retrieve.require' => '请选择是否开启挽回优惠',
        'is_retrieve.in' => '挽回优惠开关值错误',
//        'retrieve_amount.requireIf' => '请输入优惠金额',
//        'retrieve_amount.float' => '优惠金额值错误',
//        'retrieve_amount.egt' => '优惠金额必须大于等于0',
//        'retrieve_amount.elt' => '优惠金额必须小于等于实际售价',
        'sort.number' => '排序值错误',
        'status.require' => '请选择状态',
        'status.in' => '状态值错误',
        'is_default.require' => '请选择是否默认',
        'is_default.in' => '是否默认值错误',
        'is_perpetual.require' => '请选择是否永久',
        'is_perpetual.in' => '永久值错误',
        'chat_limit.number' => '每日对话上限必须为纯数字',
        'chat_limit.gt' => '每日对话上限必须大于0',
        'benefits_ids.array' => '会员权益值错误',
        'give_draw_number.number' => '赠送绘画值错误',
        'give_chat_number.number' => '赠送绘画值错误',
        'member_time_view.array' => '会员有效期显示值格式错误',
        'is_quota.require' => '请选择是否限购',
        'is_quota.in' => '是否限购值错误',
        'quota_value.number' => '限购值错误',
        'quota_value.gt' => '限购值必须大于0',
    ];


    public function sceneAdd()
    {
        return $this->only(['name','duration','duration_type','sell_price','lineation_price','is_retrieve','sort','status','is_perpetual','chat_limit','benefits_ids','give_draw_number','give_chat_number','is_quota','quota_value']);
    }

    public function sceneDetail()
    {
        return $this->only(['id']);
    }

    public function sceneEdit()
    {
        return $this->only(['id','name','duration','duration_type','sell_price','lineation_price','is_retrieve','sort','status','is_perpetual','chat_limit','benefits_ids','give_draw_number','give_chat_number','is_quota','quota_value']);
    }

    public function sceneDel()
    {
        return $this->only(['id'])
            ->append('id','checkDel');
    }

    public function sceneStatus()
    {
        return $this->only(['id']);
    }

    public function sceneDefault()
    {
        return $this->only(['id']);
    }

    public function sceneSetConfig()
    {
        return $this->only(['status','member_time_view'])
            ->remove('status','');
    }

    /**
     * @notes 删除套餐验证
     * @param $value
     * @return bool|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author cjhao
     * @date 2023/7/12 11:46
     */
    public function checkDel($value)
    {
        $cardCodeList = CardCode::where(['relation_id' => $value,'type'=>CardCodeEnum::TYPE_MEMBER])->select();
        $now = time();
        foreach ($cardCodeList as $cardCode){
            if($cardCode->valid_end_time > $now){
                return '已有卡密关联了该套餐，不允许删除';
            }
        }

        $userMemberList = UserMember::field('id,member_end_time,is_perpetual')
            ->where(['package_info->id' => (int)$value,'refund_status'=>PayEnum::REFUND_NOT])
            ->setFieldType(['package_info->id' => 'int'])
            ->select()->toArray();
        if (!empty($userMemberList)) {
            return '用户正在使用该套餐，不允许删除';
        }
//        foreach ($userMemberList as $userMember){
//            if($userMember['is_perpetual']){
//                return '用户正在使用该套餐，不允许删除';
//            } elseif (!empty($userMember['member_end_time']) && $userMember['member_end_time'] > $now) {
//                return '用户正在使用该套餐，不允许删除';
//            }
//        }

        return true;
    }


    /**
     * @notes 校验优惠金额
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2023/11/23 2:27 下午
     */
    public function checkRetrieve($value,$rule,$data)
    {
        if ($data['is_retrieve'] == 1) {
            if (!isset($data['retrieve_amount']) || empty($data['retrieve_amount'])) {
                return '请输入优惠金额';
            }
            if (!Validate::float($data['retrieve_amount'])) {
                return '优惠金额值错误';
            }
            if ($data['retrieve_amount'] < 0) {
                return '优惠金额必须大于等于0';
            }
            if ($data['retrieve_amount'] > $data['sell_price']) {
                return '优惠金额必须小于等于实际售价';
            }
        }

        return true;
    }
}
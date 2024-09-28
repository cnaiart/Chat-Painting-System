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

namespace app\common\logic;

use app\common\enum\MemberPackageEnum;
use app\common\enum\PayEnum;
use app\common\enum\user\AccountLogEnum;
use app\common\model\member\MemberOrder;
use app\common\model\recharge\RechargeOrder;
use app\common\model\user\User;
use app\common\model\user\UserMember;
use think\facade\Db;
use think\facade\Log;

/**
 * 支付成功后处理订单状态
 * Class PayNotifyLogic
 * @package app\api\logic
 */
class PayNotifyLogic extends BaseLogic
{

    public static function handle($action, $orderSn, $extra = [])
    {
        Db::startTrans();
        try {
            self::$action($orderSn, $extra);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            Log::write(implode('-', [
                __CLASS__,
                __FUNCTION__,
                $e->getFile(),
                $e->getLine(),
                $e->getMessage()
            ]));
            self::setError($e->getMessage());
            return $e->getMessage();
        }
    }


    /**
     * @notes 充值回调
     * @param $orderSn
     * @param array $extra
     * @author 段誉
     * @date 2023/2/27 15:28
     */
    public static function recharge($orderSn, $extra = [])
    {
        $order = RechargeOrder::where('sn', $orderSn)->findOrEmpty();
        $recharge_package = json_decode($order->recharge_package_info,true);
        // 增加用户累计充值金额及用户余额
        $user = User::findOrEmpty($order->user_id);
        $user->total_amount += $order->order_amount;

        $user->balance += $order->number;
        $user->balance_draw += $order->draw_number;
        $user->save();

        // 记录账户流水
        if (isset($recharge_package['number']) && $recharge_package['number'] > 0) {
            AccountLogLogic::add(
                $order->user_id,
                AccountLogEnum::UM_INC_RECHARGE,
                AccountLogEnum::INC,
                $recharge_package['number'],
                $order->sn
            );
        }
        if (isset($recharge_package['is_give']) && $recharge_package['is_give'] == 1 && isset($recharge_package['give_number']) && $recharge_package['give_number'] > 0) {
            AccountLogLogic::add(
                $order->user_id,
                AccountLogEnum::UM_INC_RECHARGE_GIVE,
                AccountLogEnum::INC,
                $recharge_package['give_number'],
                $order->sn
            );
        }
        if (isset($recharge_package['draw_number']) && $recharge_package['draw_number'] > 0) {
            AccountLogLogic::add(
                $order->user_id,
                AccountLogEnum::DRAW_INC_RECHARGE,
                AccountLogEnum::INC,
                $recharge_package['draw_number'],
                $order->sn
            );
        }
        if (isset($recharge_package['is_give']) && $recharge_package['is_give'] == 1 && isset($recharge_package['give_draw_number']) && $recharge_package['give_draw_number'] > 0) {
            AccountLogLogic::add(
                $order->user_id,
                AccountLogEnum::DRAW_INC_RECHARGE_GIVE,
                AccountLogEnum::INC,
                $recharge_package['give_draw_number'],
                $order->sn
            );
        }

        // 更新充值订单状态
        $order->transaction_id = $extra['transaction_id'];
        $order->pay_status = PayEnum::ISPAID;
        $order->pay_time = time();
        $order->save();


        //分销
        (new DistributionLogic())->add($user,$order,1);
    }


    /**
     * @notes 会员订单支付回调
     * @param $orderSn
     * @param array $extra
     * @author ljj
     * @date 2023/4/20 6:27 下午
     */
    public static function member($orderSn, $extra = [])
    {
        $time = time();
        $order = MemberOrder::where('sn', $orderSn)->findOrEmpty();
        $member_package = json_decode($order->member_package_info,true);

        //赠送绘画次数
        $give_draw_number = (isset($member_package['give_draw_number']) && $member_package['give_draw_number']) ? $member_package['give_draw_number'] : 0;
        //赠送对话次数
        $give_chat_number = (isset($member_package['give_chat_number']) && $member_package['give_chat_number']) ? $member_package['give_chat_number'] : 0;

        //是否永久套餐
        $is_perpetual = $member_package['is_perpetual'] ?? 0;
        $user = User::findOrEmpty($order->user_id);
        //永久套餐
        if($is_perpetual){
            $user->member_perpetual = 1;
        }else{
            //增加用户会员到期时间
            $duration_type = $member_package['duration_type'] ?? 1;
            switch ($duration_type) {
                case MemberPackageEnum::DURATION_TYPE_MONTH:
                    $add_time = strtotime("+{$member_package['duration']} month");
                    break;
                case MemberPackageEnum::DURATION_TYPE_DAY:
                    $add_time = strtotime("+{$member_package['duration']} day");
                    break;
            }
            if ($user->member_end_time > $time) {
                $member_end_time = $user->member_end_time - $time + $add_time;
            } else {
                $member_end_time = $add_time;
            }
            $user->member_end_time = $member_end_time;
        }
        $user->total_amount += $order->order_amount;
        $user->balance_draw += $give_draw_number;
        $user->balance += $give_chat_number;
        $user->save();

        //记录账户流水
        if ($give_draw_number > 0) {
            AccountLogLogic::add(
                $order->user_id,
                AccountLogEnum::DRAW_INC_MEMBER_GIVE,
                AccountLogEnum::INC,
                $give_draw_number,
                $order->sn
            );
        }
        //记录账户流水
        if ($give_chat_number > 0) {
            AccountLogLogic::add(
                $order->user_id,
                AccountLogEnum::UM_INC_MEMBER_GIVE,
                AccountLogEnum::INC,
                $give_chat_number,
                $order->sn
            );
        }

        // 更新订单支付状态
        $order->transaction_id = $extra['transaction_id'] ?? '';
        $order->pay_status = PayEnum::ISPAID;
        $order->pay_time = time();
        $order->save();


        //分销
        (new DistributionLogic())->add($user,$order,2);


        //添加会员开通记录
        UserMember::create([
            'user_id' => $order->user_id,
            'operate_id' => $order->user_id,
            'order_id' => $order->id,
            'channel' => MemberPackageEnum::CHANNEL_USER,
            'package_name' => $member_package['name'],
            'member_end_time' => $user->member_end_time,
            'is_perpetual' => $is_perpetual,
            'package_info' => $order->member_package_info,
            'add_member_time' => (isset($add_time) && !empty($add_time)) ? ($add_time - time()) : null,
        ]);
    }
}

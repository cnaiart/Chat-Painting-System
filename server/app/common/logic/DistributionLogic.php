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

namespace app\common\logic;


use app\common\enum\user\AccountLogEnum;
use app\common\model\distribution\DistributionOrder;
use app\common\model\user\User;
use app\common\service\ConfigService;

class DistributionLogic extends BaseLogic
{
    /**
     * @notes 添加分销订单
     * @param $user
     * @param $order
     * @param $order_type
     * @return bool
     * @author ljj
     * @date 2023/5/25 11:06 上午
     */
    public function add($user,$order,$order_type)
    {
        //分销开关
        $distribution_open = ConfigService::get('distribution','is_open',0);
        if ($distribution_open == 0) {
            return true;
        }

        //直属上级
        if (!$user['first_leader']) {
            return true;
        }
        $first_leader = User::where('id',$user['first_leader'])->findOrEmpty()->toArray();
        if (empty($first_leader)) {
            return true;
        }
        $distribution_first_ratio = 0;//一级分销收益比例
        $distribution_first_reward = 0;//一级分销收益
        if ($first_leader['is_distribution'] == 1 && $first_leader['distribution_status'] == 1) {
            //计算一级分销收益
            $distribution_first_ratio = ConfigService::get('distribution','first_ratio',0);//一级分销收益比例
            $distribution_first_reward = round($order['order_amount'] * $distribution_first_ratio / 100,2);//一级分销收益
        }
        $user['first_leader'] = $distribution_first_reward > 0 ? $user['first_leader'] : 0;

        //上上级
        $distribution_second_ratio = 0;
        $distribution_second_reward = 0;
        if ($user['second_leader']) {
            $second_leader = User::where('id',$user['second_leader'])->findOrEmpty()->toArray();
            if (!empty($second_leader) && $second_leader['is_distribution'] == 1 && $second_leader['distribution_status'] == 1) {
                $distribution_level = ConfigService::get('distribution','level',1);//分销收益等级
                if ($distribution_level == 2) {
                    $distribution_second_ratio = ConfigService::get('distribution','second_ratio',0);//二级分销收益比例
                    $distribution_second_reward = round($order['order_amount'] * $distribution_second_ratio / 100,2);//二级分销收益
                }
            }
        }
        $user['second_leader'] = $distribution_second_reward > 0 ? $user['second_leader'] : 0;


        if ($distribution_first_reward > 0 || $distribution_second_reward > 0) {
            //添加分销订单
            DistributionOrder::create([
                'order_type' => $order_type,
                'order_id' => $order['id'],
                'order_sn' => $order['sn'],
                'order_amount' => $order['order_amount'],
                'pay_time' => $order['pay_time'],
                'user_id' => $order['user_id'],
                'first_user_id' => $user['first_leader'],
                'first_ratio' => $distribution_first_ratio,
                'first_reward' => $distribution_first_reward,
                'second_user_id' => $user['second_leader'],
                'second_ratio' => $distribution_second_ratio,
                'second_reward' => $distribution_second_reward,
            ]);
        }
        

        //收益到账
        if ($distribution_first_reward > 0) {
            // 增加用户可提现金额
            User::update(['user_money'=>['inc',$distribution_first_reward],'total_user_money'=>['inc',$distribution_first_reward]],['id'=>$user['first_leader']]);

            // 记录账户流水
            AccountLogLogic::add(
                $user['first_leader'],
                AccountLogEnum::MONEY_INC_DISTRIBUTION,
                AccountLogEnum::INC,
                $distribution_first_reward,
                $order['sn']
            );
        }
        if ($distribution_second_reward > 0) {
            // 增加用户可提现金额
            User::update(['user_money'=>['inc',$distribution_second_reward],'total_user_money'=>['inc',$distribution_second_reward]],['id'=>$user['second_leader']]);

            // 记录账户流水
            AccountLogLogic::add(
                $user['second_leader'],
                AccountLogEnum::MONEY_INC_DISTRIBUTION,
                AccountLogEnum::INC,
                $distribution_second_reward,
                $order['sn']
            );
        }

        return true;
    }
}
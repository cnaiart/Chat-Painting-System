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
namespace app\common\service\user;
use app\common\enum\user\AccountLogEnum;
use app\common\logic\AccountLogLogic;
use app\common\logic\BaseLogic;
use app\common\model\user\User;
use app\common\service\ConfigService;


class UserService extends BaseLogic
{
    /**
     * @notes 注册事件
     * @param $user_id
     * @return bool|void
     * @author ljj
     * @date 2023/10/7 2:56 下午
     */
    public function registerEvent($user_id)
    {
        $user = User::findOrEmpty($user_id);
        if($user->isEmpty()){
            return true;
        }

        //注册奖励
        $register_reward_status = ConfigService::get('register_reward', 'status');
        if ($register_reward_status == 1) {
            $balance = ConfigService::get('register_reward', 'reward');
            $balance_draw = ConfigService::get('register_reward', 'reward_draw');
            if (!empty($balance) && $balance > 0) {
                $user->balance = $balance;
            }
            if (!empty($balance_draw) && $balance_draw > 0) {
                $user->balance_draw = $balance_draw;
            }
        }

        //是否自动成为分销商
        $is_open = ConfigService::get('distribution','is_open');
        $condition = ConfigService::get('distribution','condition',1);
        if ($is_open == 1 && $condition == 1) {
            $user->is_distribution = 1;
            $user->distribution_time = time();
        }
        $user->save();


        if (isset($balance) && !empty($balance) && $balance > 0) {
            // 记录账户流水
            AccountLogLogic::add(
                $user->id,
                AccountLogEnum::UM_INC_REGISTER,
                AccountLogEnum::INC,
                $balance
            );
        }
        if (isset($balance_draw) && !empty($balance_draw) && $balance_draw > 0) {
            // 记录账户流水
            AccountLogLogic::add(
                $user->id,
                AccountLogEnum::DRAW_INC_REGISTER,
                AccountLogEnum::INC,
                $balance_draw
            );
        }
    }

}
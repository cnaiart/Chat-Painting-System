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

namespace app\adminapi\logic\distribution;


use app\common\enum\DistributionEnum;
use app\common\logic\BaseLogic;
use app\common\model\distribution\DistributionOrder;
use app\common\model\user\User;

class DistributorLogic extends BaseLogic
{
    /**
     * @notes 开通分销商
     * @param $params
     * @return bool
     * @author ljj
     * @date 2023/5/23 5:34 下午
     */
    public function add($params)
    {
        User::update(['is_distribution'=>1,'distribution_time'=>time()],['id'=>$params['user_ids']]);
        return true;
    }

    /**
     * @notes 分销商详情
     * @param $params
     * @return mixed
     * @author ljj
     * @date 2023/5/23 6:03 下午
     */
    public static function detail($params)
    {
        $result = User::alias('U1')
            ->leftjoin('user U2', 'U2.id = U1.first_leader')
            ->field('U1.id,U1.avatar,U1.nickname,U1.real_name,U1.user_money,U1.total_user_money,U2.nickname as leader_nickname,U2.is_distribution as leader_is_distribution,U1.distribution_status,U1.distribution_time')
            ->where(['U1.id' => $params['id']])
            ->findOrEmpty()->toArray();

        $result['below_num'] = User::where('first_leader|second_leader','=',$result['id'])->count();//下级人数
        $result['below_distribution_num'] = User::where('first_leader|second_leader','=',$result['id'])->where(['is_distribution'=>1])->count();//下级分销商人数
        $result['distribution_order_num'] = DistributionOrder::where('first_user_id|second_user_id','=',$result['id'])->count();//分销订单
        $result['distribution_status_desc'] = DistributionEnum::getDistributionStatusDesc($result['distribution_status']);//分销状态
        $result['distribution_time'] = date('Y-m-d H:i:s',$result['distribution_time']);//成为分销商时间

        return $result;
    }

    /**
     * @notes 修改分销状态
     * @param $params
     * @return bool
     * @author ljj
     * @date 2023/5/23 6:15 下午
     */
    public function status($params)
    {
        $user = User::findOrEmpty($params['id']);
        $user->distribution_status = ($user->distribution_status == 1) ? 0 : 1;
        $user->save();
        return true;
    }
}
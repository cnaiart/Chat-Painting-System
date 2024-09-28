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

namespace app\adminapi\lists\distribution;


use app\adminapi\lists\BaseAdminDataLists;
use app\common\enum\DistributionEnum;
use app\common\lists\ListsExtendInterface;
use app\common\model\user\User;

class DistributorBelowLists extends BaseAdminDataLists implements ListsExtendInterface
{
    /**
     * @notes 搜索条件
     * @return array
     * @author ljj
     * @date 2023/5/23 6:36 下午
     */
    public function where()
    {
        $where[] = ['U1.first_leader|U1.second_leader','=',$this->params['id']];
        if(isset($this->params['user_keyword']) && $this->params['user_keyword'] != ''){
            $where[] = ['U1.sn|U1.nickname|U1.mobile','like','%'.$this->params['user_keyword'].'%'];
        }
        if(isset($this->params['is_distribution']) && $this->params['is_distribution'] != ''){
            $where[] = ['U1.is_distribution','=',$this->params['is_distribution']];
        }
        if(isset($this->params['type']) && $this->params['type'] != ''){
            switch ($this->params['type']) {
                case 1:
                    $where[] = ['U1.first_leader','=',$this->params['id']];
                    break;
                case 2:
                    $where[] = ['U1.second_leader','=',$this->params['id']];
                    break;
            }
        }

        return $where;

    }

    /**
     * @notes 下级列表
     * @return array
     * @author ljj
     * @date 2023/5/23 6:44 下午
     */
    public function lists(): array
    {
        $lists = User::alias('U1')
            ->leftjoin('user U2', 'U2.id = U1.first_leader')
            ->field('U1.id,U1.avatar,U1.nickname,U1.sn as user_sn,U1.user_money,U1.total_user_money,U2.nickname as leader_nickname,U1.is_distribution,U1.distribution_status,U1.distribution_time,U1.create_time')
            ->where($this->where())
            ->limit($this->limitOffset, $this->limitLength)
            ->select()
            ->toArray();

        foreach ($lists as $key => $list) {
            $lists[$key]['is_distribution_desc'] = $list['is_distribution'] == 1 ? '已开通' : '未开通';
            $lists[$key]['distribution_time'] = empty($list['distribution_time']) ? '' : date('Y-m-d H:i:s',$list['distribution_time']);
            $lists[$key]['distribution_status_desc'] = DistributionEnum::getDistributionStatusDesc($list['distribution_status']);
        }

        return $lists;
    }

    /**
     * @notes 下级人数
     * @return int
     * @author ljj
     * @date 2023/5/23 6:45 下午
     */
    public function count(): int
    {
        $count = User::alias('U1')
            ->leftjoin('user U2', 'U2.id = U1.first_leader')
            ->where($this->where())
            ->count();
        return $count;
    }

    /**
     * @notes 统计数据
     * @return array
     * @author ljj
     * @date 2023/5/23 6:48 下午
     */
    public function extend()
    {
        $user = User::where('id','=',$this->params['id'])->findOrEmpty()->toArray();
        $below_first_num = User::where('first_leader','=',$this->params['id'])->count();
        $below_first_distribution_num = User::where('first_leader','=',$this->params['id'])->where(['is_distribution'=>1])->count();
        $below_second_num = User::where('second_leader','=',$this->params['id'])->count();
        $below_second_distribution_num = User::where('second_leader','=',$this->params['id'])->where(['is_distribution'=>1])->count();
        $first_num = User::alias('U1')->leftjoin('user U2', 'U2.id = U1.first_leader')->where($this->where())->where('U1.first_leader','=',$this->params['id'])->count();
        $second_num = User::alias('U1')->leftjoin('user U2', 'U2.id = U1.first_leader')->where($this->where())->where('U1.second_leader','=',$this->params['id'])->count();
        return [
            'info' => [
                'user_name' => $user['nickname'],
                'user_sn' => $user['sn'],
                'below_num' => $below_first_num + $below_second_num,
                'below_distribution_num' => $below_first_distribution_num + $below_second_distribution_num,
                'below_first_num' => $below_first_num,
                'below_first_distribution_num' => $below_first_distribution_num,
                'below_second_num' => $below_second_num,
                'below_second_distribution_num' => $below_second_distribution_num,
            ],
            'lists' => [
                'all_num' => $first_num + $second_num,
                'first_num' => $first_num,
                'second_num' => $second_num,
            ],
        ];
    }
}
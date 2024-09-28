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
use app\common\lists\ListsExcelInterface;
use app\common\model\user\User;

class DistributorLists extends BaseAdminDataLists implements ListsExcelInterface
{
    /**
     * @notes 搜索条件
     * @return array
     * @author ljj
     * @date 2023/5/23 4:38 下午
     */
    public function where()
    {
        $where[] = ['U1.is_distribution','=',1];
        if(isset($this->params['user_keyword']) && $this->params['user_keyword'] != ''){
            $where[] = ['U1.sn|U1.nickname|U1.mobile','like','%'.$this->params['user_keyword'].'%'];
        }
        if(isset($this->params['leader_keyword']) && $this->params['leader_keyword'] != ''){
            $where[] = ['U2.sn|U2.nickname|U2.mobile','like','%'.$this->params['leader_keyword'].'%'];
        }
        if(isset($this->params['distribution_status']) && $this->params['distribution_status'] != ''){
            $where[] = ['U1.distribution_status','=',$this->params['distribution_status']];
        }
        if(isset($this->params['start_time']) && $this->params['start_time'] != ''){
            $where[] = ['U1.distribution_time','>',strtotime($this->params['start_time'])];
        }
        if(isset($this->params['end_time']) && $this->params['end_time'] != ''){
            $where[] = ['U1.distribution_time','<',strtotime($this->params['end_time'])];
        }
        return $where;

    }

    /**
     * @notes 分销商列表
     * @return array
     * @author ljj
     * @date 2023/5/23 5:10 下午
     */
    public function lists(): array
    {
        $lists = User::alias('U1')
            ->leftjoin('user U2', 'U2.id = U1.first_leader')
            ->field('U1.id,U1.avatar,U1.nickname,U1.sn as user_sn,U1.user_money,U1.total_user_money,U2.nickname as leader_nickname,U1.distribution_status,U1.distribution_time')
            ->where($this->where())
            ->order('U1.distribution_time','desc')
            ->limit($this->limitOffset, $this->limitLength)
            ->group('U1.id')
            ->select()
            ->toArray();

        foreach ($lists as $key => $list) {
            $lists[$key]['below_num'] = User::where('first_leader|second_leader','=',$list['id'])->count();
            $lists[$key]['distribution_status_desc'] = DistributionEnum::getDistributionStatusDesc($list['distribution_status']);
            $lists[$key]['distribution_time'] = date('Y-m-d H:i:s',$list['distribution_time']);
        }

        return $lists;
    }

    /**
     * @notes 分销商数量
     * @return int
     * @author ljj
     * @date 2023/5/23 5:10 下午
     */
    public function count(): int
    {
        $count = User::alias('U1')
            ->leftjoin('user U2', 'U1.id = U2.first_leader')
            ->where($this->where())
            ->group('U1.id')
            ->count();
        return $count;
    }

    /**
     * @notes 导出文件名
     * @return string
     * @author ljj
     * @date 2023/8/24 2:49 下午
     */
    public function setFileName(): string
    {
        return '分销商列表';
    }

    /**
     * @notes 导出字段
     * @return string[]
     * @author ljj
     * @date 2023/8/24 2:49 下午
     */
    public function setExcelFields(): array
    {
        return [
            'nickname' => '用户昵称',
            'user_money' => '可提现佣金',
            'total_user_money' => '获得总佣金',
            'below_num' => '下级人数',
            'leader_nickname' => '上级邀请人',
            'distribution_status_desc' => '分销状态',
            'distribution_time' => '成为分销商时间',
        ];
    }
}
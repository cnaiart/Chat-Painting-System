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

namespace app\api\lists;


use app\common\lists\ListsExtendInterface;
use app\common\model\user\User;

class DistributionFansLists extends BaseApiDataLists implements ListsExtendInterface
{
    /**
     * @notes 搜索条件
     * @return array
     * @author ljj
     * @date 2023/5/25 9:53 上午
     */
    public function where()
    {
        $where[] = ['first_leader|second_leader','=',$this->userId];
        if (isset($this->params['level']) && $this->params['level'] != '') {
            switch ($this->params['level']) {
                case 1:
                    $where[] = ['first_leader','=',$this->userId];
                    break;
                case 2:
                    $where[] = ['second_leader','=',$this->userId];
                    break;
            }
        }

        return $where;
    }

    /**
     * @notes 粉丝列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2023/5/25 9:59 上午
     */
    public function lists(): array
    {
        $lists = User::field('id,avatar,nickname,total_amount,create_time,is_distribution,distribution_time')
            ->append(['order_num','invite_num'])
            ->where($this->where())
            ->order('id','desc')
            ->limit($this->limitOffset, $this->limitLength)
            ->select()
            ->toArray();

        foreach ($lists as $key => $list) {
            $lists[$key]['is_distribution_desc'] = $list['is_distribution'] == 1 ? '已开通' : '未开通';
            $lists[$key]['distribution_time'] = empty($list['distribution_time']) ? '' : date('Y-m-d H:i:s',$list['distribution_time']);
        }

        return $lists;
    }

    /**
     * @notes 粉丝数量
     * @return int
     * @author ljj
     * @date 2023/5/25 9:59 上午
     */
    public function count(): int
    {
        return User::where($this->where())->count();
    }

    /**
     * @notes 数据统计
     * @return array
     * @author ljj
     * @date 2023/5/25 5:20 下午
     */
    public function extend()
    {
        return [
            'all' => User::where(['first_leader|second_leader'=>$this->userId])->count(),
            'first' => User::where(['first_leader'=>$this->userId])->count(),
            'second' => User::where(['second_leader'=>$this->userId])->count(),
        ];
    }
}
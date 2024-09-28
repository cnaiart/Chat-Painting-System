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
use app\common\lists\ListsExcelInterface;
use app\common\model\distribution\DistributionOrder;

class DistributionOrderLists extends BaseAdminDataLists implements ListsExcelInterface
{
    /**
     * @notes 搜索条件
     * @return array
     * @author ljj
     * @date 2023/5/24 10:28 上午
     */
    public function where()
    {
        $where = [];
        if (isset($this->params['order_sn']) && $this->params['order_sn'] != '') {
            $where[] = ['do.order_sn','like','%'.$this->params['order_sn'].'%'];
        }
        if (isset($this->params['user_info']) && $this->params['user_info'] != '') {
            $where[] = ['u.sn|u.nickname|u.mobile','like','%'.$this->params['user_info'].'%'];
        }
        if (isset($this->params['distributor_info']) && $this->params['distributor_info'] != '') {
            $where[] = ['d1.sn|d1.nickname|d1.mobile|d2.sn|d2.nickname|d2.mobile','like','%'.$this->params['distributor_info'].'%'];
        }
        if(isset($this->params['start_time']) && $this->params['start_time'] != ''){
            $where[] = ['do.pay_time','>',strtotime($this->params['start_time'])];
        }
        if(isset($this->params['end_time']) && $this->params['end_time'] != ''){
            $where[] = ['do.pay_time','<',strtotime($this->params['end_time'])];
        }

        return $where;
    }

    /**
     * @notes 分销订单列表
     * @return array
     * @author ljj
     * @date 2023/5/24 10:34 上午
     */
    public function lists(): array
    {
        $lists = DistributionOrder::alias('do')
            ->leftjoin('user u', 'u.id = do.user_id')
            ->leftjoin('user d1', 'd1.id = do.first_user_id')
            ->leftjoin('user d2', 'd2.id = do.second_user_id')
            ->field('do.id,u.avatar,u.nickname,u.sn as user_sn,do.order_type,do.order_sn,do.order_amount,d1.nickname as first_name,do.first_ratio,do.first_reward,d2.nickname as second_name,do.second_ratio,do.second_reward,do.pay_time')
            ->append(['order_type_desc','pay_time_desc'])
            ->hidden(['order_type','pay_time'])
            ->where($this->where())
            ->order('do.id','desc')
            ->limit($this->limitOffset, $this->limitLength)
            ->select()
            ->toArray();

        return $lists;
    }

    /**
     * @notes 分销订单数量
     * @return int
     * @author ljj
     * @date 2023/5/24 10:35 上午
     */
    public function count(): int
    {
        return DistributionOrder::alias('do')
            ->leftjoin('user u', 'u.id = do.user_id')
            ->leftjoin('user d1', 'd1.id = do.first_user_id')
            ->leftjoin('user d2', 'd2.id = do.second_user_id')
            ->where($this->where())
            ->count();
    }

    /**
     * @notes 导出文件名
     * @return string
     * @author ljj
     * @date 2023/8/24 2:49 下午
     */
    public function setFileName(): string
    {
        return '分销订单列表';
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
            'order_sn' => '订单编号',
            'order_amount' => '实付金额',
            'first_reward' => '一级分销奖励',
            'second_reward' => '二级分销奖励',
            'pay_time_desc' => '支付时间',
        ];
    }
}
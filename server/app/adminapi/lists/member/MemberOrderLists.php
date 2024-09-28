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

namespace app\adminapi\lists\member;


use app\adminapi\lists\BaseAdminDataLists;
use app\common\enum\PayEnum;
use app\common\lists\ListsExcelInterface;
use app\common\lists\ListsExtendInterface;
use app\common\model\member\MemberOrder;

class MemberOrderLists extends BaseAdminDataLists implements ListsExtendInterface,ListsExcelInterface
{
    /**
     * @notes 搜索条件
     * @return array
     * @author ljj
     * @date 2023/4/21 9:42 上午
     */
    public function where()
    {
        $where[] = ['mo.pay_status','=',PayEnum::ISPAID];
        if (isset($this->params['user_info']) && $this->params['user_info'] != '') {
            $where[] = ['u.sn|u.nickname|u.mobile','like','%'.$this->params['user_info'].'%'];
        }
        if (isset($this->params['terminal']) && $this->params['terminal'] != '') {
            $where[] = ['mo.terminal','=',$this->params['terminal']];
        }
        if (isset($this->params['refund_status']) && $this->params['refund_status'] != '') {
            $where[] = ['mo.refund_status','=',$this->params['refund_status']];
        }
        if (isset($this->params['start_time']) && $this->params['start_time'] != '') {
            $where[] = ['mo.pay_time','>=',strtotime($this->params['start_time'])];
        }
        if (isset($this->params['end_time']) && $this->params['end_time'] != '') {
            $where[] = ['mo.pay_time','<=',strtotime($this->params['end_time'])];
        }

        return $where;
    }

    /**
     * @notes 会员订单列表
     * @return array
     * @author ljj
     * @date 2023/4/21 9:44 上午
     */
    public function lists(): array
    {
        $lists = MemberOrder::alias('mo')
            ->join('user u', 'u.id = mo.user_id')
            ->field('mo.id,mo.sn,u.avatar,u.nickname,u.sn as user_sn,mo.pay_status,mo.pay_time,mo.order_amount,mo.member_package_info,mo.refund_status,mo.create_time,u.mobile')
            ->append(['member_package','pay_status_text','pay_time_text','refund_status_text'])
            ->where($this->where())
            ->order('mo.id', 'desc')
            ->limit($this->limitOffset, $this->limitLength)
            ->select()
            ->toArray();

        return $lists;
    }

    /**
     * @notes 会员订单数量
     * @return int
     * @author ljj
     * @date 2023/4/21 9:44 上午
     */
    public function count(): int
    {
        return MemberOrder::alias('mo')
            ->join('user u', 'u.id = mo.user_id')
            ->where($this->where())
            ->count();
    }

    /**
     * @notes 统计数据
     * @return array
     * @author ljj
     * @date 2023/4/21 9:45 上午
     */
    public function extend()
    {
        $total_amount = MemberOrder::where(['pay_status'=>PayEnum::ISPAID])->sum('order_amount');
        $refund_total_amount = MemberOrder::where(['pay_status'=>PayEnum::ISPAID,'refund_status'=>PayEnum::REFUND_SUCCESS])->sum('order_amount');
        return [
            'order_num' => MemberOrder::where(['pay_status'=>PayEnum::ISPAID])->count(),
            'total_amount' => $total_amount,
            'refund_order_num' => MemberOrder::where(['pay_status'=>PayEnum::ISPAID,'refund_status'=>PayEnum::REFUND_SUCCESS])->count(),
            'refund_total_amount' => $refund_total_amount,
            'net_income' => round($total_amount - $refund_total_amount,2),
        ];
    }

    /**
     * @notes 导出文件名
     * @return string
     * @author ljj
     * @date 2023/8/24 2:49 下午
     */
    public function setFileName(): string
    {
        return '会员订单列表';
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
            'sn' => '订单编号',
            'nickname' => '用户昵称',
            'mobile' => '手机号码',
            'member_package' => '购买套餐',
            'order_amount' => '实付金额',
            'pay_status_text' => '支付状态',
            'refund_status_text' => '退款状态',
            'pay_time_text' => '支付时间',
        ];
    }
}
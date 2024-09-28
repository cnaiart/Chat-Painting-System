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
use app\common\enum\WithdrawEnum;
use app\common\lists\ListsExcelInterface;
use app\common\lists\ListsExtendInterface;
use app\common\model\distribution\WithdrawApply;

class WithdrawLists extends BaseAdminDataLists implements ListsExtendInterface,ListsExcelInterface
{
    /**
     * @notes 搜索条件
     * @return array
     * @author ljj
     * @date 2023/5/24 2:23 下午
     */
    public function where()
    {
        $where = [];
        if (isset($this->params['status']) && $this->params['status'] != '') {
            $where[] = ['w.status','=',$this->params['status']];
        }
        if (isset($this->params['user_info']) && $this->params['user_info'] != '') {
            $where[] = ['u.sn|u.nickname|u.mobile','like','%'.$this->params['user_info'].'%'];
        }
        if (isset($this->params['type']) && $this->params['type'] != '') {
            $where[] = ['w.type','=',$this->params['type']];
        }
        if (isset($this->params['start_time']) && $this->params['start_time'] != '') {
            $where[] = ['w.create_time','>=',strtotime($this->params['start_time'])];
        }
        if (isset($this->params['end_time']) && $this->params['end_time'] != '') {
            $where[] = ['w.create_time','<=',strtotime($this->params['end_time'])];
        }

        return $where;
    }

    /**
     * @notes 提现记录列表
     * @return array
     * @author ljj
     * @date 2023/5/24 2:23 下午
     */
    public function lists(): array
    {
        $lists = WithdrawApply::alias('w')
            ->join('user u', 'u.id = w.user_id')
            ->field('w.id,u.avatar,u.nickname,u.sn as user_sn,w.money,w.handling_fee,w.left_money,w.type,w.status,w.create_time')
            ->append(['type_desc','status_desc','handling_fee_ratio'])
            ->where($this->where())
            ->order('w.id','desc')
            ->limit($this->limitOffset, $this->limitLength)
            ->select()
            ->toArray();

        return $lists;
    }

    /**
     * @notes 提现记录数量
     * @return int
     * @author ljj
     * @date 2023/5/24 2:24 下午
     */
    public function count(): int
    {
        return WithdrawApply::alias('w')
            ->join('user u', 'u.id = w.user_id')
            ->where($this->where())
            ->count();
    }

    /**
     * @notes 统计数据
     * @return array
     * @author ljj
     * @date 2023/5/29 3:23 下午
     */
    public function extend()
    {
        return [
            'all_num' => WithdrawApply::alias('w')->join('user u', 'u.id = w.user_id')->count(),
            'wait_num' => WithdrawApply::alias('w')->join('user u', 'u.id = w.user_id')->where(['status'=>WithdrawEnum::STATUS_WAIT])->count(),
            'ing_num' => WithdrawApply::alias('w')->join('user u', 'u.id = w.user_id')->where(['status'=>WithdrawEnum::STATUS_ING])->count(),
            'success_num' => WithdrawApply::alias('w')->join('user u', 'u.id = w.user_id')->where(['status'=>WithdrawEnum::STATUS_SUCCESS])->count(),
            'fail_num' => WithdrawApply::alias('w')->join('user u', 'u.id = w.user_id')->where(['status'=>WithdrawEnum::STATUS_FAIL])->count(),
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
        return '提现记录列表';
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
            'money' => '提现金额',
            'handling_fee' => '手续费',
            'left_money' => '到账金额',
            'type_desc' => '提现方式',
            'status_desc' => '提现状态',
            'create_time' => '申请时间',
        ];
    }
}
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
namespace app\adminapi\lists\distribution;
use app\adminapi\lists\BaseAdminDataLists;
use app\common\enum\DistributionEnum;
use app\common\lists\ListsExcelInterface;
use app\common\lists\ListsExtendInterface;
use app\common\model\distribution\DistributionApply;
use app\common\service\FileService;

/**
 * 分销申请列表类
 * Class DistributionApplyLists
 * @package app\adminapi\lists\distribution
 */
class DistributionApplyLists extends BaseAdminDataLists implements ListsExtendInterface,ListsExcelInterface
{

    /**
     * @notes 设置查询条件
     * @return array
     * @author cjhao
     * @date 2023/5/23 11:50
     */
    public function setSearch()
    {
        $where = [];
        if(isset($this->params['user_keyword']) && $this->params['user_keyword'] != ''){
            $where[] = ['U1.sn|U1.nickname|U1.mobile','like','%'.$this->params['user_keyword'].'%'];
        }
        if(isset($this->params['leader_keyword']) && $this->params['leader_keyword'] != ''){
            $where[] = ['U2.sn|U2.nickname|U2.mobile','like','%'.$this->params['leader_keyword'].'%'];
        }
        if(isset($this->params['start_time']) && $this->params['start_time'] != ''){
            $where[] = ['U1.create_time','>',strtotime($this->params['start_time'])];
        }
        if(isset($this->params['end_time']) && $this->params['end_time'] != ''){
            $where[] = ['U1.create_time','<',strtotime($this->params['end_time'])];
        }
        if(isset($this->params['status']) && $this->params['status'] != ''){
            $where[] = ['DA.status','=',$this->params['status']];
        }
        return $where;

    }
    /**
     * @notes 实现数据列表
     * @return array
     * @author 令狐冲
     * @date 2021/7/6 00:33
     */
    public function lists(): array
    {
        $lists = DistributionApply::alias('DA')
            ->join('user U1', 'DA.user_id = U1.id')
            ->leftjoin('user U2', 'U2.id = U1.first_leader')
            ->field('DA.id,DA.user_id,DA.name,DA.mobile,DA.create_time,DA.status,U1.nickname,U1.avatar,U1.sn as user_sn,U2.nickname as leader_nickname')
            ->where($this->setSearch())
            ->order('DA.id','desc')
            ->limit($this->limitOffset, $this->limitLength)
            ->select()->toArray();

        foreach ($lists as $key => $list)
        {
            empty($list['leader_nickname']) && $lists[$key]['leader_nickname'] = '系统';
            $lists[$key]['status_desc'] = DistributionEnum::getStatusDesc($list['status']);
            $lists[$key]['avatar'] = FileService::getFileUrl($list['avatar']);
        }

        return $lists;

    }

    /**
     * @notes 实现数据列表记录数
     * @return int
     * @author 令狐冲
     * @date 2021/7/6 00:34
     */
    public function count(): int
    {
        $count = DistributionApply::alias('DA')
            ->join('user U1', 'DA.user_id = U1.id')
            ->leftjoin('user U2', 'U2.id = U1.first_leader')
            ->where($this->setSearch())
            ->count();
        return $count;
    }

    /**
     * @notes 扩展字段
     * @return mixed
     * @author 令狐冲
     * @date 2021/7/21 17:45
     */
    public function extend()
    {
        $where = $this->setSearch();
        foreach ($where as $key=>$val) {
            if ($val[0] == 'DA.status') {
                unset($where[$key]);
            }
        }
        //全部数量
        $allCount = DistributionApply::alias('DA')
            ->join('user U1', 'DA.user_id = U1.id')
            ->leftjoin('user U2', 'U2.id = U1.first_leader')
            ->where($where)
            ->count();
        //审核数量
        $auditCount = DistributionApply::alias('DA')
            ->join('user U1', 'DA.user_id = U1.id')
            ->leftjoin('user U2', 'U2.id = U1.first_leader')
            ->where($where)
            ->where(['status'=>DistributionEnum::AUDIT_ING])
            ->count();
        //通过数量
        $passCount = DistributionApply::alias('DA')
            ->join('user U1', 'DA.user_id = U1.id')
            ->leftjoin('user U2', 'U2.id = U1.first_leader')
            ->where($where)
            ->where(['status'=>DistributionEnum::AUDIT_PASS])
            ->count();
        //拒接数量
        $refuseCount = DistributionApply::alias('DA')
            ->join('user U1', 'DA.user_id = U1.id')
            ->leftjoin('user U2', 'U2.id = U1.first_leader')
            ->where($where)
            ->where(['status'=>DistributionEnum::AUDIT_FAILT])
            ->count();
        return [
            'all_count'         => $allCount,
            'audit_count'       => $auditCount,
            'pass_count'        => $passCount,
            'refuse_count'      => $refuseCount,
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
        return '分销申请列表';
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
            'name' => '姓名',
            'mobile' => '手机号码',
            'leader_nickname' => '上级邀请人',
            'status_desc' => '审核状态',
            'create_time' => '申请时间',
        ];
    }
}
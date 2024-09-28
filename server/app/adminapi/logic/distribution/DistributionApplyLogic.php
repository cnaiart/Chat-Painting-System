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
namespace app\adminapi\logic\distribution;
use app\common\enum\DistributionEnum;
use app\common\model\distribution\DistributionApply;
use app\common\model\user\User;
use Exception;
use think\facade\Db;

/**
 * 分销申请逻辑类
 * Class DistributionApplyLogic
 * @package app\adminapi\logic\distribution
 */
class DistributionApplyLogic
{

    /**
     * @notes 获取分销申请详情
     * @param int $id
     * @return mixed
     * @author cjhao
     * @date 2023/5/23 15:07
     */
    public static function detail(int $id)
    {
        $detail = DistributionApply::alias('DA')
            ->join('user U1', 'DA.user_id = U1.id')
            ->leftjoin('user U2', 'U2.id = U1.first_leader')
            ->field('DA.id,DA.user_id,DA.name,DA.mobile,DA.create_time,DA.status,U1.nickname,U2.nickname as leader_nickname,DA.audit_time,DA.audit_remark')
            ->where(['DA.id' => $id])
            ->findOrEmpty()->toArray();

        $detail['status_desc'] = DistributionEnum::getStatusDesc($detail['status']);
        $detail['audit_time'] = empty($detail['audit_time']) ? '-' : date('Y-m-d H:i:s',$detail['audit_time']);

        return $detail;

    }
    /**
     * @notes 申请审核
     * @param array $post
     * @author cjhao
     * @date 2023/5/23 14:59
     */
    public static function audit(array $post)
    {
        try {
            Db::startTrans();
            $apply = DistributionApply::where(['id'=>$post['id']])->findOrEmpty();
            if($apply->isEmpty()){
                throw new Exception('申请不存在');
            }
            if(DistributionEnum::AUDIT_ING != $apply->status){
                throw new Exception('申请已处理，请勿重复操作');
            }
            $apply->status = $post['status'];
            $apply->audit_remark = $post['remark'] ?? '';
            $apply->audit_time = time();
            $apply->save();
            if(DistributionEnum::AUDIT_PASS == $apply->status){
                User::where(['id'=>$apply->user_id])->update(['is_distribution'=>1,'distribution_time'=>time()]);
            }
            Db::commit();
            return true;
        }catch (Exception $e){
            // 回滚事务
            Db::rollback();
            return $e->getMessage();
        }
    }

}
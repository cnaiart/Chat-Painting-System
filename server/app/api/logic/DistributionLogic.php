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
namespace app\api\logic;
use app\common\enum\DistributionEnum;
use app\common\enum\user\AccountLogEnum;
use app\common\enum\WithdrawEnum;
use app\common\model\distribution\DistributionApply;
use app\common\model\distribution\DistributionOrder;
use app\common\model\distribution\WithdrawApply;
use app\common\model\user\User;
use app\common\model\user\UserAccountLog;
use app\common\service\ConfigService;
use think\Exception;
use think\facade\Db;

/**
 * 分销逻辑类
 * Class DistributionLogic
 * @package app\api\logic
 */
class DistributionLogic
{

    /**
     * @notes 分销中心
     * @param int $userId
     * @return array
     * @author cjhao
     * @date 2023/5/22 18:46
     */
    public function index(int $userId):array
    {
        $isOpen = ConfigService::get('distribution', 'is_open');
        $user = User::field('id,nickname,avatar,first_leader,user_money,total_user_money,is_distribution,sn')
            ->findOrEmpty($userId);
        $user->first_leader_nickname = '';
        if($user->first_leader){
            $user->first_leader_nickname = User::where(['id'=>$user->first_leader])->value('nickname');
        }
        //今日收益
        $user->today_money = DistributionOrder::where(['first_user_id'=>$userId])->whereDay('create_time')->sum('first_reward') + DistributionOrder::where(['second_user_id'=>$userId])->whereDay('create_time')->sum('second_reward');
        $user->today_money = round($user->today_money,2);
        //已提现
        $user->withdrawn_money = WithdrawApply::where(['user_id'=>$userId,'status'=>WithdrawEnum::STATUS_SUCCESS])->sum('money');
        $user->withdrawn_money = round($user->withdrawn_money,2);

        $applyDetail = [];
        if(0 == $user->is_distribution){
            $applyDetail = DistributionApply::where(['user_id'=>$userId])
                ->field('name,mobile,audit_remark,status')
                ->order('id desc')
                ->findOrEmpty();
        }

        $accountLog = UserAccountLog::field('change_type,action,change_amount,create_time')
            ->append(['change_type_desc'])
            ->hidden(['change_type'])
            ->where(['user_id'=>$userId,'change_type'=>AccountLogEnum::getUserMoneyChangeType()])
            ->order('id', 'desc')
            ->limit(5)
            ->select()
            ->toArray();

        //分销配置
        $config = [
            'is_open'           => ConfigService::get('distribution','is_open',0),
            'condition'         => ConfigService::get('distribution','condition',1),
            'auto_audit'        => ConfigService::get('distribution','auto_audit',0),
            'level'             => ConfigService::get('distribution','level',1),
            'first_ratio'       => ConfigService::get('distribution','first_ratio',0),
            'second_ratio'      => ConfigService::get('distribution','second_ratio',0),
            'pc_promotion_url'  => request()->domain().'/?user_sn='.$user->sn,//pc端推广链接
        ];

        //提现配置
        $ali_withdraw_info = WithdrawApply::where(['user_id'=>$userId,'type'=>[WithdrawEnum::TYPE_ALI,WithdrawEnum::TYPE_ALI_QRCODE]])->order('id','desc')->findOrEmpty()->toArray();
        $wechat_withdraw_info = WithdrawApply::where(['user_id'=>$userId,'type'=>WithdrawEnum::TYPE_WECHAT_QRCODE])->order('id','desc')->findOrEmpty()->toArray();
        $withdraw_config = [
            //提现开关：1-开启；0-关闭
            'open' => ConfigService::get('withdraw', 'open',1),
            //提现方式：1-支付宝
            'type' => ConfigService::get('withdraw', 'type',[1]),
            //提现说明
            'explain' => ConfigService::get('withdraw', 'explain'),
            //最近使用的支付宝账号
            'ali_acccount' => $ali_withdraw_info['account'] ?? '',
            //最近使用的支付宝姓名
            'ali_name' => $ali_withdraw_info['real_name'] ?? '',
            //最近使用的微信账号
            'wechat_acccount' => $wechat_withdraw_info['account'] ?? '',
            //最近使用的微信姓名
            'wechat_name' => $wechat_withdraw_info['real_name'] ?? '',
        ];

        return [
            'is_open'       => $isOpen,
            'user'          => $user,
            'apply_detail'  => $applyDetail,
            'account_log'   => $accountLog,
            'config'        => $config,
            'withdraw_config'        => $withdraw_config,
        ];


    }

    /**
     * @notes 分销申请
     * @param array $post
     * @author cjhao
     * @date 2023/5/18 18:38
     */
    public function distributionApply(array $post,int $userId)
    {
        try{
            Db::startTrans();
            $isOpen = ConfigService::get('distribution', 'is_open',0);
            if(0 == $isOpen){
                throw new Exception('分销功能未开启');
            }
            $user = User::findOrEmpty($userId);
            if($user->is_distribution){
                throw new Exception('您已是分销商，请勿重复申请');
            }
            $distributionApply = DistributionApply::where(['user_id' => $userId,'status'=>0])->findOrEmpty();
            if(!$distributionApply->isEmpty()){
                throw new Exception('你的申请正在审核中，请勿重复提交');
            }
            $post['status'] = DistributionEnum::AUDIT_ING;
            $post['user_id'] = $userId;
            $autoAudit = ConfigService::get('distribution','auto_audit',0);
            if(1 == $autoAudit){
                $post['status'] = DistributionEnum::AUDIT_PASS;
                $post['audit_time'] = time();
                $user->is_distribution = 1;
                $user->distribution_time = time();
                $user->save();
            }
            (new DistributionApply())->save($post);

            Db::commit();
            return true;

        }catch (\Exception $e){
            Db::rollback();
            return $e->getMessage();

        }
    }




}
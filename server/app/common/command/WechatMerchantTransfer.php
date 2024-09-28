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

namespace app\common\command;


use app\common\enum\user\AccountLogEnum;
use app\common\enum\WithdrawEnum;
use app\common\logic\AccountLogLogic;
use app\common\logic\WechatMerchantTransferLogic;
use app\common\model\distribution\WithdrawApply;
use app\common\model\user\User;
use app\common\service\pay\AliPayService;
use think\console\Command;
use think\console\Output;
use think\console\Input;

class WechatMerchantTransfer extends Command
{
    protected function configure()
    {
        $this->setName('wechat_merchant_transfer')
            ->setDescription('转账结果查询');
    }

    protected function execute(Input $input, Output $output)
    {
        $lists = WithdrawApply::where(['type'=>[WithdrawEnum::TYPE_WECHAT,WithdrawEnum::TYPE_ALI],'status'=>WithdrawEnum::STATUS_ING])
            ->field('id,sn,batch_no,user_id,money,type')
            ->order('id','desc')
            ->select()
            ->toArray();

        foreach ($lists as $list) {
            if ($list['type'] == WithdrawEnum::TYPE_WECHAT) {
                try {
                    $result = WechatMerchantTransferLogic::details($list);
                } catch (\Exception $e) {
                    //调用接口失败，更新提现申请单
                    WithdrawApply::update([
                        'query_result' => json_encode($e->getMessage(), JSON_UNESCAPED_UNICODE),
                    ],['id'=>$list['id']]);
                    continue;
                }

                if (isset($result['detail_status']) && $result['detail_status'] == 'SUCCESS') {
                    //提现成功，更新提现申请单
                    WithdrawApply::update([
                        'status' => WithdrawEnum::STATUS_SUCCESS,
                        'query_result' => json_encode($result, JSON_UNESCAPED_UNICODE),
                        'finish_time' => time(),
                    ],['id'=>$list['id']]);
                } elseif (isset($result['detail_status']) && $result['detail_status'] == 'FAIL') {
                    //提现失败，更新提现申请单
                    WithdrawApply::update([
                        'status' => WithdrawEnum::STATUS_FAIL,
                        'query_result' => json_encode($result, JSON_UNESCAPED_UNICODE),
                        'finish_time' => time(),
                    ],['id'=>$list['id']]);

                    //回退提现金额
                    User::update(['user_money'=>['inc',$list['money']]],['id'=>$list['user_id']]);
                    // 记录账户流水
                    AccountLogLogic::add(
                        $list['user_id'],
                        AccountLogEnum::MONEY_INC_WITHDRAW_FAIL,
                        AccountLogEnum::INC,
                        $list['money'],
                        $list['sn']
                    );
                } else {
                    //查询信息错误
                    WithdrawApply::update(['query_result' => json_encode($result, JSON_UNESCAPED_UNICODE)],['id'=>$list['id']]);
                }
            }
            if ($list['type'] == WithdrawEnum::TYPE_ALI) {
                $result = (new AliPayService())->transferQuery($list);
                //更新查询结果
                WithdrawApply::update([
                    'query_result' => json_encode($result,JSON_UNESCAPED_UNICODE),
                ],['id'=>$list['id']]);
                if(isset($result['status']) && $result['status'] == 'SUCCESS'){//转账成功
                    //更新提现状态
                    WithdrawApply::update([
                        'status' => WithdrawEnum::STATUS_SUCCESS,
                        'finish_time' => time(),
                    ],['id'=>$list['id']]);
                } elseif (isset($result['status']) && $result['status'] == 'FAIL') {//转账失败
                    //更新提现状态
                    WithdrawApply::update([
                        'status' => WithdrawEnum::STATUS_FAIL,
                        'finish_time' => time(),
                    ],['id'=>$list['id']]);

                    //回退提现金额
                    User::update(['user_money'=>['inc',$list['money']]],['id'=>$list['user_id']]);
                    // 记录账户流水
                    AccountLogLogic::add(
                        $list['user_id'],
                        AccountLogEnum::MONEY_INC_WITHDRAW_FAIL,
                        AccountLogEnum::INC,
                        $list['money'],
                        $list['sn']
                    );
                }
            }

        }

        return true;
    }
}
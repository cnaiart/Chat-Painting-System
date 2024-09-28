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

namespace app\adminapi\logic\distribution;


use app\common\enum\user\AccountLogEnum;
use app\common\enum\WithdrawEnum;
use app\common\logic\AccountLogLogic;
use app\common\logic\BaseLogic;
use app\common\logic\WechatMerchantTransferLogic;
use app\common\model\distribution\WithdrawApply;
use app\common\model\user\User;
use app\common\service\ConfigService;
use app\common\service\pay\AliPayService;
use think\facade\Db;

class WithdrawLogic extends BaseLogic
{
    /**
     * @notes 提现审核
     * @param $params
     * @return bool|string
     * @author ljj
     * @date 2023/5/24 3:18 下午
     */
    public function verify($params)
    {
        Db::startTrans();
        try {
            $withdraw_apply = WithdrawApply::findOrEmpty($params['id'])->toArray();

            switch($params['verify_status']) {
                case WithdrawEnum::VERIFY_STATUS_SUCCESS://审核通过
                    //更新审核状态
                    WithdrawApply::update([
                        'status' => WithdrawEnum::STATUS_ING,
                        'verify_status' => WithdrawEnum::VERIFY_STATUS_SUCCESS,
                        'verify_remark' => $params['verify_remark'] ?? '',
                        'verify_time' => time(),
                    ],['id'=>$withdraw_apply['id']]);

                    switch ($withdraw_apply['type']) {
                        // 提现至支付宝
                        case WithdrawEnum::TYPE_ALI:
                            // 校验条件
                            if($withdraw_apply['left_money'] < 0.1 || $withdraw_apply['left_money'] > 100000000) {
                                throw new \think\Exception('扣除手续费后提现金额不能小于0.1元或大于100000000元');
                            }

                            //转账到支付宝账号
                            $result = (new AliPayService())->transfer($withdraw_apply);
                            //更新转账结果
                            WithdrawApply::update([
                                'transfer_result' => json_encode($result,JSON_UNESCAPED_UNICODE),
                            ],['id'=>$withdraw_apply['id']]);
                            if(isset($result['status']) && $result['status'] == 'SUCCESS') {//转账成功
                                //更新提现状态
                                WithdrawApply::update([
                                    'status' => WithdrawEnum::STATUS_SUCCESS,
                                    'finish_time' => time(),
                                ],['id'=>$withdraw_apply['id']]);
                            } elseif (isset($result['status']) && $result['status'] == 'FAIL' && (!isset($result['sub_code']) || $result['sub_code'] != 'SYSTEM_ERROR')) {//转账失败
                                //更新提现状态
                                WithdrawApply::update([
                                    'status' => WithdrawEnum::STATUS_FAIL,
                                    'finish_time' => time(),
                                ],['id'=>$withdraw_apply['id']]);

                                //回退提现金额
                                User::update(['user_money'=>['inc',$withdraw_apply['money']]],['id'=>$withdraw_apply['user_id']]);
                                // 记录账户流水
                                AccountLogLogic::add(
                                    $withdraw_apply['user_id'],
                                    AccountLogEnum::MONEY_INC_WITHDRAW_FAIL,
                                    AccountLogEnum::INC,
                                    $withdraw_apply['money'],
                                    $withdraw_apply['sn']
                                );
                            }

                            break;
                        // 提现至微信零钱
                        case WithdrawEnum::TYPE_WECHAT:
                            // 校验条件
                            if($withdraw_apply['left_money'] < 1) {
                                throw new \think\Exception('扣除手续费后提现金额不能小于1元');
                            }
                            $count = WithdrawApply::whereDay('update_time')->where([
                                ['user_id', '=', $withdraw_apply['user_id']],
                                ['type', '=', WithdrawEnum::TYPE_WECHAT],
                                ['status', 'in', [WithdrawEnum::STATUS_ING,WithdrawEnum::STATUS_SUCCESS,WithdrawEnum::STATUS_FAIL]],
                            ])->count();
                            if($count >= 10) {
                                throw new \think\Exception('同一天向同一个用户最多付款10次');
                            }

                            //商家转账到零钱
                            WechatMerchantTransferLogic::transfer($withdraw_apply);
                            break;
                    }

                    break;
                case WithdrawEnum::VERIFY_STATUS_FAIL://审核拒绝
                    //修改提现申请状态
                    WithdrawApply::update([
                        'status' => WithdrawEnum::STATUS_FAIL,
                        'verify_status' => WithdrawEnum::VERIFY_STATUS_FAIL,
                        'verify_remark' => $params['verify_remark'],
                        'verify_time' => time(),
                        'finish_time' => time(),
                    ],['id'=>$withdraw_apply['id']]);

                    //回退提现金额
                    User::update(['user_money'=>['inc',$withdraw_apply['money']]],['id'=>$withdraw_apply['user_id']]);

                    // 记录账户流水
                    AccountLogLogic::add(
                        $withdraw_apply['user_id'],
                        AccountLogEnum::MONEY_INC_WITHDRAW_FAIL,
                        AccountLogEnum::INC,
                        $withdraw_apply['money'],
                        $withdraw_apply['sn']
                    );

                    break;
            }

            Db::commit();
            return true;
        } catch(\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
    }


    /**
     * @notes 转账
     * @param $params
     * @return bool
     * @author ljj
     * @date 2023/5/24 3:34 下午
     */
    public function transfer($params)
    {
        Db::startTrans();
        try {
            $withdraw_apply = WithdrawApply::findOrEmpty($params['id']);
            $withdraw_apply->status = ($params['transfer_status'] == 1) ? WithdrawEnum::STATUS_SUCCESS : WithdrawEnum::STATUS_FAIL;
            $withdraw_apply->transfer_remark = $params['transfer_remark'] ?? '';
            $withdraw_apply->finish_time = time();
            $withdraw_apply->save();

            //提现失败
            if ($withdraw_apply->status == WithdrawEnum::STATUS_FAIL) {
                //回退提现金额
                User::update(['user_money'=>['inc',$withdraw_apply->money]],['id'=>$withdraw_apply->user_id]);

                // 记录账户流水
                AccountLogLogic::add(
                    $withdraw_apply->user_id,
                    AccountLogEnum::MONEY_INC_WITHDRAW_FAIL,
                    AccountLogEnum::INC,
                    $withdraw_apply->money,
                    $withdraw_apply->sn
                );
            }

            // 提交事务
            Db::commit();
            return true;
        } catch(\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
    }

    /**
     * @notes 提现详情
     * @param $params
     * @return mixed
     * @author ljj
     * @date 2023/5/24 3:52 下午
     */
    public function detail($params)
    {
        $result = WithdrawApply::alias('wa')
            ->leftJoin('user u', 'u.id = wa.user_id')
            ->field('wa.id,wa.type,wa.money,wa.left_money,wa.handling_fee,wa.real_name,wa.account,wa.status,wa.verify_remark,wa.verify_time,wa.transfer_remark,wa.finish_time,wa.create_time,u.nickname,wa.money_qr_code')
            ->append(['type_desc','status_desc','handling_fee_ratio'])
            ->findOrEmpty($params['id'])
            ->toArray();

        $result['verify_time'] = empty($result['verify_time']) ? '' : date('Y-m-d H:i:s',$result['verify_time']);
        $result['finish_time'] = empty($result['finish_time']) ? '' : date('Y-m-d H:i:s',$result['finish_time']);

        return $result;
    }

    /**
     * @notes 转账查询
     * @param $params
     * @return false|string
     * @author ljj
     * @date 2023/6/20 5:14 下午
     */
    public function search($params)
    {
        Db::startTrans();
        try {
            $withdrawApply = WithdrawApply::findOrEmpty($params['id']);

            $result = WechatMerchantTransferLogic::details($withdrawApply);
            $tips =  '未知';
            if(isset($result['detail_status'])) {
                if ($result['detail_status'] == 'SUCCESS') {
                    //提现成功，更新提现申请单
                    WithdrawApply::update([
                        'status' => WithdrawEnum::STATUS_SUCCESS,
                        'query_result' => json_encode($result, JSON_UNESCAPED_UNICODE),
                        'finish_time' => time(),
                    ],['id'=>$params['id']]);

                    $tips = '提现成功';
                }
                if ($result['detail_status'] == 'FAIL') {
                    //提现失败，更新提现申请单
                    WithdrawApply::update([
                        'status' => WithdrawEnum::STATUS_FAIL,
                        'query_result' => json_encode($result, JSON_UNESCAPED_UNICODE),
                        'finish_time' => time(),
                    ],['id'=>$withdrawApply['id']]);

                    //回退提现金额
                    User::update(['user_money'=>['inc',$withdrawApply['money']]],['id'=>$withdrawApply['user_id']]);
                    // 记录账户流水
                    AccountLogLogic::add(
                        $withdrawApply['user_id'],
                        AccountLogEnum::MONEY_INC_WITHDRAW_FAIL,
                        AccountLogEnum::INC,
                        $withdrawApply['money'],
                        $withdrawApply['sn']
                    );

                    $tips =  '提现失败';
                }
                if ($result['detail_status'] == 'PROCESSING') {
                    $tips =  '正在处理中';
                }
            }else {
                // 查询失败
                throw new \think\Exception($result['message'] ?? '商家转账到零钱查询失败');
            }

            // 提交事务
            Db::commit();
            // 返回提示消息
            return $tips;
        } catch(\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 获取提现配置
     * @return array
     * @author ljj
     * @date 2023/5/24 4:16 下午
     */
    public function getConfig()
    {
        return [
            //提现方式：1-支付宝；2-微信零钱；3-微信收款码；4-支付宝收款码；
            'type' => ConfigService::get('withdraw', 'type',[1]),
            //微信零钱接口：1-企业付款到零钱；2-商家转账到零钱
//            'wechat_way' => ConfigService::get('withdraw', 'wechat_way',1),
            //最低提现金额
            'min_money' => ConfigService::get('withdraw', 'min_money'),
            //最高提现金额
            'max_money' => ConfigService::get('withdraw', 'max_money'),
            //提现手续费
            'handling_fee' => ConfigService::get('withdraw', 'handling_fee'),
            //提现说明开关：1-开启；0-关闭
            'open' => ConfigService::get('withdraw', 'open',1),
            //提现说明
            'explain' => ConfigService::get('withdraw', 'explain'),
        ];
    }

    /**
     * @notes 设置提现配置
     * @param array $params
     * @return bool
     * @author ljj
     * @date 2023/5/24 4:31 下午
     */
    public function setConfig(array $params):bool
    {
        //提现方式：1-支付宝；2-微信零钱；3-微信收款码；4-支付宝收款码；
        ConfigService::set('withdraw', 'type',$params['type']);
        //微信零钱接口：1-企业付款到零钱；2-商家转账到零钱
//        ConfigService::set('withdraw', 'wechat_way',$params['wechat_way']);
        //最低提现金额
        ConfigService::set('withdraw', 'min_money', empty($params['min_money']) ? '' : round($params['min_money'],2));
        //最高提现金额
        ConfigService::set('withdraw', 'max_money', empty($params['max_money']) ? '' : round($params['max_money'],2));
        //提现手续费
        ConfigService::set('withdraw', 'handling_fee', empty($params['handling_fee']) ? '' : round($params['handling_fee'],2));
        //提现说明开关：1-开启；0-关闭
        ConfigService::set('withdraw', 'open',$params['open']);
        //提现说明
        ConfigService::set('withdraw', 'explain', $params['explain']);

        return true;
    }
}
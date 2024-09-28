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
use app\common\enum\CardCodeEnum;
use app\common\enum\MemberPackageEnum;
use app\common\enum\PayEnum;
use app\common\enum\user\AccountLogEnum;
use app\common\logic\AccountLogLogic;
use app\common\logic\BaseLogic;
use app\common\model\cardcode\CardCode;
use app\common\model\cardcode\CardCodeRecord;
use app\common\model\member\MemberPackage;
use app\common\model\recharge\RechargePackage;
use app\common\model\user\User;
use app\common\model\user\UserMember;
use think\Exception;
use think\facade\Cache;
use think\facade\Db;

/**
 * 卡密兑换逻辑类
 * Class CardCodeLogic
 * @package app\api\logic
 */
class CardCodeLogic extends BaseLogic
{


    /**
     * @notes 获取卡密
     * @param string $sn
     * @param string $userId
     * @return array|string
     * @author cjhao
     * @date 2023/7/11 16:29
     */
    public function checkCard(string $sn,int $userId)
    {
        try{
            $cardCode = $this->checkSn($sn)['card_code'];
            $content = '';
            $validTime = '';
            $now = time();
            switch ($cardCode->type){
                case CardCodeEnum::TYPE_MEMBER:
                    $memberPackge = MemberPackage::where(['id'=>$cardCode->relation_id])->field('duration,duration_type,is_perpetual,name,id')->findOrEmpty();
                    $duration = $memberPackge['duration'] ?? '';
                    $content = $memberPackge['name'] ?? '';
                    $isPerpetual = $memberPackge['is_perpetual'] ?? '';
                    $durationType = $memberPackge['duration_type'] ?? 0;
                    $validTime = '永久';
                    if($isPerpetual){
                        $content.= '(永久)';
                    }else{

                        if ($durationType == MemberPackageEnum::DURATION_TYPE_DAY) {
                            $content.= '('.$duration.'天)';
                            $addTime = strtotime("+{$duration} day");
                        } else {
                            $content.= '('.$duration.'个月)';
                            $addTime = strtotime("+{$duration} month");
                        }
                        $user = User::findOrEmpty($userId);
                        if(0 == $user->member_perpetual){
                            if ($user->member_end_time > $now) {
                                $validTime = $user->member_end_time - $now + $addTime;
                            } else {
                                $validTime = $addTime;
                            }
                            $validTime = date('Y-m-d H:i:s',$validTime);
                        }
                    }
                    break;
                case CardCodeEnum::TYPE_RECHARGE:
                    $content = RechargePackage::where(['id'=>$cardCode->relation_id])->value('name ');
                    break;
                case CardCodeEnum::TYPE_CHAT_NUM:
                    $content = $cardCode->chat_num.'条';
                    break;
                case CardCodeEnum::TYPE_DRAW_NUM:
                    $content = $cardCode->draw_num.'条';
                    break;
            }
            return [
                'id'            => $cardCode->id,
                'sn'            => $cardCode->sn,
                'type'          => $cardCode->type,
                'type_desc'     => CardCodeEnum::getTypeDesc($cardCode->type),
                'content'       => $content,
                'valid_time'    => $validTime,
                'failure_time'  => date('Y-m-d H:i:s',$cardCode->valid_end_time).' 前可使用'
            ];
        }catch (Exception $e){
            return $e->getMessage();
        }

        
    }

    /**
     * @notes 卡密兑换
     * @param $sn
     * @author cjhao
     * @date 2023/7/11 17:11
     */
    public function useCard($sn,$userId)
    {
        try{

            $cache = Cache::get('card_code_'.$sn);
            Cache::set('card_code_'.$sn,$sn,2);
            if($cache){
                throw new Exception('请勿频繁操作');
            }

            Db::startTrans();
            $cardData = $this->checkSn($sn);
            $cardCode = $cardData['card_code'];
            $cardCodeRecord = $cardData['card_code_record'];
            $user = User::findOrEmpty($userId);
            //会员套餐
            if(CardCodeEnum::TYPE_MEMBER == $cardCode->type){
                $package = MemberPackage::where(['id'=>$cardCode->relation_id])->findOrEmpty();
                if($package->isEmpty()){
                    throw new Exception('卡密兑换失败');
                }
                $giveDrawNumber = (isset($package['give_draw_number']) && $package['give_draw_number']) ? $package['give_draw_number'] : 0;
                $giveChatNumber = (isset($package['give_chat_number']) && $package['give_chat_number']) ? $package['give_chat_number'] : 0;

                //是否永久套餐
                $isPerpetual = $package['is_perpetual'] ?? 0;
                $now = time();
                //永久套餐
                if($isPerpetual){
                    $user->member_perpetual = 1;
                }else{
                    //增加用户会员到期时间
//                    $days = date("t");//当前月份天数
//                    $addTime = $package['duration'] * $days * 24 * 60 * 60;//增加的时间
                    $duration_type = $package['duration_type'] ?? 1;
                    switch ($duration_type) {
                        case MemberPackageEnum::DURATION_TYPE_MONTH:
                            $add_time = strtotime("+{$package['duration']} month");
                            break;
                        case MemberPackageEnum::DURATION_TYPE_DAY:
                            $add_time = strtotime("+{$package['duration']} day");
                            break;
                    }

                    if ($user->member_end_time > $now) {
                        $memberEndTime = $user->member_end_time - $now + $add_time;
                    } else {
                        $memberEndTime = $add_time;
                    }
                    $user->member_end_time = $memberEndTime;
                }
                $user->balance_draw += $giveDrawNumber;
                $user->balance += $giveChatNumber;
                $user->save();

                //记录账户流水
                if ($giveDrawNumber > 0) {
                    AccountLogLogic::add(
                        $userId,
                        AccountLogEnum::DRAW_INC_MEMBER_GIVE,
                        AccountLogEnum::INC,
                        $giveDrawNumber,
                        $cardCodeRecord->sn
                    );
                }
                //记录账户流水
                if ($giveChatNumber > 0) {
                    AccountLogLogic::add(
                        $userId,
                        AccountLogEnum::UM_INC_MEMBER_GIVE,
                        AccountLogEnum::INC,
                        $giveChatNumber,
                        $cardCodeRecord->sn
                    );
                }
                $cardCodeRecord->pachage_snapshot = $package;
                //添加会员开通记录
                UserMember::create([
                    'user_id' => $userId,
                    'operate_id' => $userId,
                    'order_id' => $cardCodeRecord->id,
                    'channel' => MemberPackageEnum::CHANNEL_CARDCODE,
                    'package_name' => $package['name'],
                    'member_end_time' => $user->member_end_time,
                    'is_perpetual' => $isPerpetual,
                    'package_info' => json_encode($package),
                    'add_member_time' => (isset($add_time) && !empty($add_time)) ? ($add_time - time()) : null,
                ]);
            }
            //兑换充值
            if(CardCodeEnum::TYPE_RECHARGE == $cardCode->type){
                $package = RechargePackage::where(['id'=>$cardCode->relation_id])->findOrEmpty();
                //用户添加次数
                $user->balance += $package->number;
                $user->balance_draw += $package->draw_number;
                $user->save();

                // 记录账户流水
                if (isset($package->number) && $package->number > 0) {
                    AccountLogLogic::add(
                        $userId,
                        AccountLogEnum::UM_INC_RECHARGE,
                        AccountLogEnum::INC,
                        $package->number ,
                        $cardCodeRecord->sn
                    );
                }
                if (isset($package->is_give) && $package->is_give == 1 && isset($package->give_number) && $package->give_number > 0) {
                    AccountLogLogic::add(
                        $userId,
                        AccountLogEnum::UM_INC_RECHARGE_GIVE,
                        AccountLogEnum::INC,
                        $package->give_number,
                        $cardCodeRecord->sn
                    );
                }
                if (isset($package->draw_number) && $package->draw_number > 0) {
                    AccountLogLogic::add(
                        $userId,
                        AccountLogEnum::DRAW_INC_RECHARGE,
                        AccountLogEnum::INC,
                        $package->draw_number,
                        $cardCodeRecord->sn
                    );
                }
                if (isset($package->is_give) && $package->is_give == 1 && isset($package->give_draw_number) && $package->give_draw_number > 0) {
                    AccountLogLogic::add(
                        $userId,
                        AccountLogEnum::DRAW_INC_RECHARGE_GIVE,
                        AccountLogEnum::INC,
                        $package->give_draw_number,
                        $cardCodeRecord->sn
                    );
                }
                $cardCodeRecord->pachage_snapshot = $package;
            }
            //兑换对话次数
            if(CardCodeEnum::TYPE_CHAT_NUM == $cardCode->type){
                $chatNum = $cardCode['chat_num'] ?? 0;
                if($chatNum > 0){
                    //用户添加次数
                    $user->balance += $chatNum;
                    $user->save();

                    //记录流水
                    AccountLogLogic::add(
                        $userId,
                        AccountLogEnum::UM_INC_CARDCODE_GIVE,
                        AccountLogEnum::INC,
                        $chatNum,
                        $cardCodeRecord->sn
                    );
                }
            }
            //兑换绘画次数
            if(CardCodeEnum::TYPE_DRAW_NUM == $cardCode->type){
                $drawNum = $cardCode['draw_num'] ?? 0;
                if($drawNum > 0){
                    //用户添加次数
                    $user->balance_draw += $drawNum;
                    $user->save();

                    //记录流水
                    AccountLogLogic::add(
                        $userId,
                        AccountLogEnum::DRAW_INC_CARDCODE_GIVE,
                        AccountLogEnum::INC,
                        $drawNum,
                        $cardCodeRecord->sn
                    );
                }
            }
            // 更新卡密兑换记录
            $cardCodeRecord->user_id = $userId;
            $cardCodeRecord->status = 1;
            $cardCodeRecord->use_time = time();
            $cardCodeRecord->save();

            Db::commit();
            return true;
        }catch (Exception $e){
            // 回滚事务
            Db::rollback();
            return $e->getMessage();
        }

    }

    /**
     * @notes 验证卡密
     * @param $sn
     * @return string
     * @author cjhao
     * @date 2023/7/11 17:03
     */
    public function checkSn($sn)
    {

        if(empty($sn)){
            throw new Exception('查询失败，请输入卡密');
        }

        $cardCodeRecord = CardCodeRecord::where(['sn'=>$sn])->findOrEmpty();
        if($cardCodeRecord->isEmpty()) {
            throw new Exception('查询失败，卡密编号不存在');
        }
        if($cardCodeRecord->status){
            throw new Exception('查询失败，卡密已被使用');
        }
        $cardCode = CardCode::where(['id' => $cardCodeRecord->card_id])->findOrEmpty();
        $now = time();
        if($now < $cardCode->valid_start_time) {
            throw new Exception('该卡密未到生效时间');
        }
        if($cardCode->valid_end_time < $now) {
            throw new Exception('卡密已过期');
        }
        return [
            'card_code'         => $cardCode,
            'card_code_record'  => $cardCodeRecord
        ];
    }

}
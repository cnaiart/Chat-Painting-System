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
namespace app\adminapi\logic\cardcode;
use app\common\enum\CardCodeEnum;
use app\common\enum\CardCodeRecordEnum;
use app\common\enum\MemberPackageEnum;
use app\common\model\cardcode\CardCode;
use app\common\model\cardcode\CardCodeRecord;
use app\common\model\member\MemberPackage;
use app\common\model\recharge\RechargePackage;
use app\common\service\ConfigService;
use think\facade\Db;

/**
 * 卡密逻辑类
 * Class CardCodeController
 * @package app\adminapi\logic\cardcode
 */
class CardCodeLogic
{

    /**
     * @notes 获取套餐
     * @return array
     * @author cjhao
     * @date 2023/7/10 15:50
     */
    public function getPackageList()
    {
        $memberPackge = MemberPackage::column('name','id');
        $rechargePackge = RechargePackage::column('name','id');
        return [
            'member_pckge'      => $memberPackge,
            'recharge_pckge'    => $rechargePackge,
        ];

    }
    /**
     * @notes 添加卡密
     * @param array $post
     * @return bool
     * @author cjhao
     * @date 2023/7/10 15:47
     */
    public function add(array $post)
    {

        try{
            Db::startTrans();
            $post['sn'] = generate_sn(CardCode::class,'sn','',0);
            $cardCode                   = new CardCode();
            $cardCode->save($post);
            $cardCodeRecord = [];
            for ($i = 0; $i < $post['card_num']; $i++) {
                $cardCodeRecord[] = [
                    'card_id'   => $cardCode->id,
                    'sn'        => generate_random_sn(CardCode::class,10,'sn'),
                ];
            }
            (new CardCodeRecord())->saveAll($cardCodeRecord);

            Db::commit();
            return true;
        }catch (\Exception $e){
            Db::rollback();
            return $e->getMessage();
        }
    }


    /**
     * @notes 卡密详情
     * @param int $id
     * @author cjhao
     * @date 2023/7/10 17:18
     */
    public function detail(int $id)
    {

        $cardCode = CardCode::where(['id' => $id])->field('id,sn,type,chat_num,draw_num,card_num,relation_id,valid_start_time,valid_end_time,create_time,remark')->findOrEmpty();
        $cardCode->type_desc = CardCodeEnum::getTypeDesc($cardCode->type);
        $cardCode->content = '';
        switch ($cardCode->type){
            case CardCodeEnum::TYPE_MEMBER:
                $memberPackge = MemberPackage::where(['id'=>$cardCode->relation_id])->field('duration,duration_type,is_perpetual,name,id')->findOrEmpty();
                $duration = $memberPackge['duration'] ?? '';
                $isPerpetual = $memberPackge['is_perpetual'] ?? '';
                $durationType = $memberPackge['duration_type'] ?? 0;
                $cardCode->content = $memberPackge['name'] ?? '';
                if($isPerpetual){
                    $cardCode->content.= '(永久)';
                }else{
                    if ($durationType == MemberPackageEnum::DURATION_TYPE_DAY) {
                        $cardCode->content.= '('.$duration.'天)';
                    } else {
                        $cardCode->content.= '('.$duration.'个月)';
                    }
                }
                break;
            case CardCodeEnum::TYPE_RECHARGE:
                $cardCode->content = RechargePackage::where(['id'=>$cardCode->relation_id])->value('name ');
                break;
            case CardCodeEnum::TYPE_CHAT_NUM:
                $cardCode->content = $cardCode->chat_num.'条';
                break;
            case CardCodeEnum::TYPE_DRAW_NUM:
                $cardCode->content = $cardCode->draw_num.'条';
                break;
        }
        $cardCode->valid_time_desc = date('Y-m-d H:i:s',$cardCode->valid_start_time).'~'.date('Y-m-d H:i:s',$cardCode->valid_end_time);
        $useNum = CardCodeRecord::where(['card_id'=>$cardCode->id,'status'=>CardCodeRecordEnum::STATYS_YES])->count();
        $cardCode->use_num = $useNum;
        $cardCode->unused_num = $cardCode->card_num - $useNum;
        return $cardCode->toArray();
    }


    /**
     * @notes 删除卡密
     * @param int $id
     * @author cjhao
     * @date 2023/7/10 17:33
     */
    public function del(int $id)
    {
        CardCode::where(['id'=>$id])->delete();
    }


    /**
     * @notes 获取卡密配置
     * @return array|int|mixed|string
     * @author cjhao
     * @date 2023/7/11 11:53
     */
    public function getConfig()
    {
        return [
            'is_show' =>  ConfigService::get('card_code','is_show'),
            'buy_site' =>  ConfigService::get('card_code','buy_site'),
            'is_open' =>  ConfigService::get('card_code','is_open',0),
        ];
    }


    /**
     * @notes 设置卡密设置
     * @param array $post
     * @author cjhao
     * @date 2023/7/11 11:55
     */
    public function setConfig(array $post)
    {
         ConfigService::set('card_code','is_show',$post['is_show']);
         ConfigService::set('card_code','buy_site',$post['buy_site']);
         ConfigService::set('card_code','is_open',$post['is_open']);
    }


}
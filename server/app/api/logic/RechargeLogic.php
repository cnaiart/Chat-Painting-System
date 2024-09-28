<?php
// +----------------------------------------------------------------------
// | likeadmin快速开发前后端分离管理后台（PHP版）
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | gitee下载：https://gitee.com/likeshop_gitee/likeadmin
// | github下载：https://github.com/likeshop-github/likeadmin
// | 访问官网：https://www.likeadmin.cn
// | likeadmin团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeadminTeam
// +----------------------------------------------------------------------

namespace app\api\logic;

use app\common\enum\PayEnum;
use app\common\logic\BaseLogic;
use app\common\model\recharge\RechargeOrder;
use app\common\model\recharge\RechargePackage;
use app\common\service\ConfigService;


/**
 * 充值逻辑层
 * Class RechargeLogic
 * @package app\shopapi\logic
 */
class RechargeLogic extends BaseLogic
{
    /**
     * @notes 充值套餐
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2023/4/18 6:51 下午
     */
    public function rechargePackage()
    {
        $result['status'] = ConfigService::get('recharge', 'status', 1);

        $result['lists'] = RechargePackage::field('id,name,image,describe,sell_price,is_recommend,number,draw_number,is_give,give_number,give_draw_number,tag')
            ->where(['status'=>1])
            ->order(['sort'=>'desc','id'=>'desc'])
            ->select()
            ->toArray();

        return $result;
    }

    /**
     * @notes 充值
     * @param array $params
     * @return array|false
     * @author ljj
     * @date 2023/4/19 2:32 下午
     */
    public static function recharge(array $params)
    {
        try {
            $recharge_package = RechargePackage::where(['id'=>$params['recharge_package_id']])->findOrEmpty()->toArray();
            $number = $recharge_package['number'];
            $draw_number = $recharge_package['draw_number'];
            if ($recharge_package['is_give'] == 1) {
                $number += $recharge_package['give_number'];
                $draw_number += $recharge_package['give_draw_number'];
            }
            $data = [
                'user_id' => $params['user_id'],
                'sn' => generate_sn(RechargeOrder::class, 'sn'),
                'terminal' => $params['terminal'],
                'order_amount' => $recharge_package['sell_price'],
                'number' => $number,
                'draw_number' => $draw_number,
                'recharge_package_id' => $params['recharge_package_id'],
                'recharge_package_info' => json_encode($recharge_package,JSON_UNESCAPED_UNICODE),
            ];
            $order = RechargeOrder::create($data);

            return [
                'order_id' => (int)$order['id'],
                'from' => 'recharge',
                'order_amount' => $recharge_package['sell_price']
            ];
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

}
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
namespace app\common\enum;
/**
 * 分销枚举类
 * Class DistributionApplyEnum
 * @package app\common\enum
 */
class DistributionEnum
{

    //审核状态
    const AUDIT_ING     = 1;
    const AUDIT_PASS    = 2;
    const AUDIT_FAILT   = 3;

    //分销状态
    const DISTRIBUTION_STATUS_NORMAL = 1;
    const DISTRIBUTION_STATUS_FREEZE = 0;

    //分销订单类型
    const ORDER_TYPE_RECHARGE = 1;//充值订单
    const ORDER_TYPE_MEMBER = 2;//会员订单


    /**
     * @notes 分销审核状态
     * @param bool $from
     * @return array|mixed|string
     * @author cjhao
     * @date 2023/5/23 12:12
     */
    public static function getStatusDesc($from = true)
    {
        $desc = [
            self::AUDIT_ING     => '待审核',
            self::AUDIT_PASS    => '审核通过',
            self::AUDIT_FAILT   => '审核拒绝',
        ];
        if(true === $desc){
            return $desc;
        }
        return $desc[$from] ?? '';

    }


    /**
     * @notes 分销状态
     * @param bool $from
     * @return bool|string
     * @author ljj
     * @date 2023/5/23 4:55 下午
     */
    public static function getDistributionStatusDesc($from = true)
    {
        $desc = [
            self::DISTRIBUTION_STATUS_NORMAL => '正常',
            self::DISTRIBUTION_STATUS_FREEZE => '冻结',
        ];
        if(true === $desc){
            return $desc;
        }
        return $desc[$from] ?? '';

    }


    /**
     * @notes 分销订单类型
     * @param bool $from
     * @return bool|string
     * @author ljj
     * @date 2023/5/24 11:01 上午
     */
    public static function getOrderTypeDesc($from = true)
    {
        $desc = [
            self::ORDER_TYPE_RECHARGE => '充值订单',
            self::ORDER_TYPE_MEMBER => '会员订单',
        ];
        if(true === $desc){
            return $desc;
        }
        return $desc[$from] ?? '';

    }
}
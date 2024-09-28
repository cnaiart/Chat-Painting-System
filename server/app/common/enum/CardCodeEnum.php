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
 * 卡密枚举类
 * Class CardCodeEnum
 * @package app\common\enum
 */
class CardCodeEnum
{

    const TYPE_MEMBER   = 1;
    const TYPE_RECHARGE = 2;
    const TYPE_CHAT_NUM = 3;
    const TYPE_DRAW_NUM = 4;

    /**
     * @notes 获取卡密类型
     * @param bool $from
     * @return bool|mixed|string
     * @author cjhao
     * @date 2023/7/10 12:22
     */
    public static function getTypeDesc($from = true)
    {
        $desc = [
            self::TYPE_MEMBER       => '会员套餐',
            self::TYPE_RECHARGE     => '充值套餐',
            self::TYPE_CHAT_NUM     => '对话次数',
            self::TYPE_DRAW_NUM     => '绘画次数',
        ];
        if(true === $from){
            return $from;
        }
        return $desc[$from] ?? '';
    }
}
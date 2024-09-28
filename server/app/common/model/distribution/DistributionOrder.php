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

namespace app\common\model\distribution;


use app\common\enum\DistributionEnum;
use app\common\model\BaseModel;
use app\common\model\user\User;
use app\common\service\FileService;

class DistributionOrder extends BaseModel
{
    /**
     * @notes 关联用户模型
     * @return \think\model\relation\HasOne
     * @author ljj
     * @date 2023/5/24 7:48 下午
     */
    public function user()
    {
        return $this->hasOne(User::class,'id','user_id')
            ->field('id,avatar,nickname');
    }


    /**
     * @notes 订单类型
     * @param $value
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2023/5/24 11:01 上午
     */
    public function getOrderTypeDescAttr($value,$data)
    {
        return DistributionEnum::getOrderTypeDesc($data['order_type']);
    }

    /**
     * @notes 支付时间
     * @param $value
     * @param $data
     * @return false|string
     * @author ljj
     * @date 2023/5/24 11:03 上午
     */
    public function getPayTimeDescAttr($value,$data)
    {
        return empty($data['pay_time']) ? '' : date('Y-m-d H:i:s',$data['pay_time']);
    }

    /**
     * @notes 订单状态
     * @param $value
     * @param $data
     * @return string
     * @author ljj
     * @date 2023/5/24 7:35 下午
     */
    public function getStatusDescAttr($value,$data)
    {
        return '已结算';
    }


    /**
     * @notes 头像获取器 - 用于头像地址拼接域名
     * @param $value
     * @return string
     * @author Tab
     * @date 2021/7/17 14:28
     */
    public function getAvatarAttr($value)
    {
        return trim($value) ? FileService::getFileUrl($value) : '';
    }

    /**
     * @notes 一级分销佣金比例
     * @param $value
     * @return int|mixed|string
     * @author ljj
     * @date 2023/5/30 2:33 下午
     */
    public function getFirstRatioAttr($value)
    {
        return trim($value) ? clear_zero($value) : '';
    }

    /**
     * @notes 一级分销佣金比例
     * @param $value
     * @return int|mixed|string
     * @author ljj
     * @date 2023/5/30 2:33 下午
     */
    public function getSecondRatioAttr($value)
    {
        return trim($value) ? clear_zero($value) : '';
    }
}
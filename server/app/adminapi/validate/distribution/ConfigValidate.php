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
namespace app\adminapi\validate\distribution;
use app\common\validate\BaseValidate;

/**
 *
 * 配置验证类
 * Class ConfigValidate
 * @package app\adminapi\validate\distibution
 */
class ConfigValidate extends BaseValidate
{

    protected $rule = [
        'is_open'       => 'require|in:0,1',
        'condition'     => 'require|in:1,2',
        'level'         => 'require|in:1,2',
        'auto_audit'    => 'requireIf:condition,2',
        'first_ratio'   => 'require|float|egt:0|elt:100',
        'second_ratio'  => 'require|float|egt:0|elt:100',
    ];

    protected $message = [
        'is_open.reuqire'       => '请选择分销状态',
        'is_open.in'            => '分销状态错误',
        'condition.require'     => '请选择分销条件',
        'condition.in'          => '分销条件错误',
        'auto_audit.requireIf'  => '请选择自动审核状态',
        'level.require'         => '请选择分销层级',
        'leve.in'               => '分销层级错误',
        'first_ratio.require'   => '请输入一级分佣比例',
        'first_ratio.float'     => '一级分佣比例值错误',
        'first_ratio.egt'       => '一级分佣比例不能小于0',
        'first_ratio.elt'       => '一级分佣比例不能大于100',
        'second_ratio.require'  => '请输入二级分佣比例',
        'second_ratio.float'    => '二级分佣比例值错误',
        'second_ratio.egt'      => '二级分佣比例不能小于0',
        'second_ratio.elt'      => '二级分佣比例不能大于100',
    ];

}
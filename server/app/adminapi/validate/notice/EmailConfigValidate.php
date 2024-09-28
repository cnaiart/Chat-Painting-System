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

namespace app\adminapi\validate\notice;


use app\common\validate\BaseValidate;

class EmailConfigValidate extends BaseValidate
{
    protected $rule = [
        'form_address' => 'require|email',
        'auth_password' => 'require',
        'smtp_host' => 'require',
        'smtp_port' => 'require',
    ];

    protected $message = [
        'form_address.require' => '请输入发件邮箱账号',
        'form_address.email' => '邮箱账号错误',
        'auth_password.require' => '请输入邮箱授权码',
        'smtp_host.require' => '请输入服务器地址',
        'smtp_port.require' => '请输入SSL端口',
    ];


    protected function sceneSetConfig()
    {
        return $this->only(['form_address','auth_password','smtp_host','smtp_port']);
    }
}
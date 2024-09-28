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

namespace app\adminapi\logic\notice;


use app\common\logic\BaseLogic;
use app\common\service\ConfigService;

class EmailConfigLogic extends BaseLogic
{
    /**
     * @notes 获取邮箱配置
     * @return array
     * @author ljj
     * @date 2023/7/20 2:22 下午
     */
    public static function getConfig()
    {
        return [
            'form_address' => ConfigService::get('email', 'form_address'),
            'auth_password' => ConfigService::get('email', 'auth_password'),
            'smtp_host' => ConfigService::get('email', 'smtp_host'),
            'smtp_port' => ConfigService::get('email', 'smtp_port'),
        ];
    }

    /**
     * @notes 设置邮箱配置
     * @param $params
     * @return bool
     * @author ljj
     * @date 2023/7/20 2:31 下午
     */
    public static function setConfig($params)
    {
        ConfigService::set('email', 'form_address',$params['form_address']);
        ConfigService::set('email', 'auth_password',$params['auth_password']);
        ConfigService::set('email', 'smtp_host',$params['smtp_host']);
        ConfigService::set('email', 'smtp_port',$params['smtp_port']);
        return true;
    }
}
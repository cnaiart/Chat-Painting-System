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

namespace app\adminapi\validate\setting;


use app\common\validate\BaseValidate;

class ContentCensorValidate extends BaseValidate
{
    protected $rule = [
        'is_open' => 'require|in:0,1',
        'app_id' => 'require',
        'api_key' => 'require',
        'secret_key' => 'require',
    ];

    protected $message = [
        'is_open.require' => '请选择是否开启百度文本审核',
        'is_open.in' => '百度文本审核值错误',
        'app_id.require' => '请输入APPID',
        'api_key.require' => '请输入APIKey',
        'secret_key.require' => '请输入SecretKey',
    ];


    public function sceneSetConfig()
    {
        return $this->only(['is_open','app_id','api_key','secret_key']);
    }
}
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

namespace app\api\validate;


use app\common\enum\notice\NoticeEnum;
use app\common\model\user\User;
use app\common\validate\BaseValidate;

class EmailValidate extends BaseValidate
{
    protected $rule = [
        'email' => 'require|email',
        'scene' => 'require',
    ];

    protected $message = [
        'email.require' => '请输入邮箱',
        'email.email' => '邮箱错误',
        'scene.require' => '请输入场景值',
    ];

    public function sceneSendCode()
    {
        return $this->only(['email','scene'])
            ->append('email','checkEmail');
    }

    /**
     * @notes 校验邮箱
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2023/9/11 4:33 下午
     */
    public function checkEmail($value, $rule, $data)
    {
        if (NoticeEnum::getSceneByTag($data['scene']) == NoticeEnum::REGISTER_CAPTCHA) {
            $user = User::where(['email'=>$value])->findOrEmpty();
            if (!$user->isEmpty()) {
                return '邮箱已被注册，请重新输入';
            }
        }

        return true;
    }
}
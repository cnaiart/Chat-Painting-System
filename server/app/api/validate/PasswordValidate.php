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
namespace app\api\validate;

use app\common\enum\LoginEnum;
use app\common\validate\BaseValidate;

/**
 * 密码校验
 * Class PasswordValidate
 * @package app\api\validate
 */
class PasswordValidate extends BaseValidate
{

    protected $regex = [
        'password' => '/^(?![0-9]+$)(?![a-z]+$)(?![A-Z]+$)(?!([^(0-9a-zA-Z)]|[\(\)])+$)([^(0-9a-zA-Z)]|[\(\)]|[a-z]|[A-Z]|[0-9]){6,20}$/'
    ];

    protected $rule = [
        'scene' => 'require|in:'.LoginEnum::MOBILE_LOGIN.','.LoginEnum::EMAIL_LOGIN.'',
        'mobile' => 'requireIf:scene,'.LoginEnum::MOBILE_LOGIN.'|mobile',
        'email' => 'requireIf:scene,'.LoginEnum::EMAIL_LOGIN.'|email',
        'password' => 'require|length:6,20|regex:password',
        'password_confirm' => 'require|confirm',
        'code' => 'require',
    ];


    protected $message = [
        'scene.require' => '场景值缺失',
        'scene.in' => '场景值错误',
        'mobile.requireIf' => '请输入手机号',
        'mobile.mobile' => '手机号错误',
        'email.requireIf' => '请输入邮箱',
        'email.email' => '邮箱错误',
        'password.require' => '请输入密码',
        'password.length' => '密码须在6-20位之间',
        'password.regex' => '密码须为数字,字母或符号组合',
        'password_confirm.require' => '请确认密码',
        'password_confirm.confirm' => '两次输入的密码不一致',
        'code.require' => '请输入验证码',
    ];


    /**
     * @notes 重置登录密码
     * @return PasswordValidate
     * @author 段誉
     * @date 2022/9/16 18:11
     */
    public function sceneResetPassword()
    {
        return $this->only(['scene','mobile','email','password','code']);
    }


    /**
     * @notes 修改密码场景
     * @return PasswordValidate
     * @author 段誉
     * @date 2022/9/20 19:14
     */
    public function sceneChangePassword()
    {
        return $this->only(['password', 'password_confirm']);
    }

}
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
use app\common\enum\notice\NoticeEnum;
use app\common\model\user\User;
use app\common\service\ConfigService;
use app\common\service\EmailService;
use app\common\service\sms\SmsDriver;
use app\common\validate\BaseValidate;

/**
 * 注册验证器
 * Class RegisterValidate
 * @package app\api\validate
 */
class RegisterValidate extends BaseValidate
{

    protected $regex = [
        'password' => '/^(?![0-9]+$)(?![a-z]+$)(?![A-Z]+$)(?!([^(0-9a-zA-Z)]|[\(\)])+$)([^(0-9a-zA-Z)]|[\(\)]|[a-z]|[A-Z]|[0-9]){6,20}$/'
    ];

    protected $rule = [
        'channel' => 'require',
        'password' => 'require|length:6,20|regex:password',
        'password_confirm' => 'require|confirm',

        'scene' => 'require|in:'.LoginEnum::MOBILE_LOGIN.','.LoginEnum::EMAIL_LOGIN.'|checkCode',
        'mobile' => 'requireIf:scene,'.LoginEnum::MOBILE_LOGIN.'|mobile|checkMobile',
        'email' => 'requireIf:scene,'.LoginEnum::EMAIL_LOGIN.'|email|checkEmail',
    ];

    protected $message = [
        'channel.require' => '注册来源参数缺失',
        'password.require' => '请输入密码',
        'password.length' => '密码须在6-20位之间',
        'password.regex' => '密码须为数字,字母或符号组合',
        'password_confirm.require' => '请确认密码',
        'password_confirm.confirm' => '两次输入的密码不一致',

        'scene.require' => '注册场景缺失',
        'scene.in' => '注册场景值错误',
        'mobile.requireIf' => '请输入手机号',
        'mobile.mobile' => '手机号错误',
        'email.requireIf' => '请输入邮箱',
        'email.email' => '邮箱错误',
    ];



    /**
     * @notes 校验验证码
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2023/7/19 10:26 上午
     */
    public function checkCode($value, $rule, $data)
    {
        $sms_verify = ConfigService::get('login', 'sms_verify', config('project.login.sms_verify'));
        if ($sms_verify == 1) {
            if (!isset($data['code']) || $data['code'] == '') {
                return '请输入验证码';
            }

            //短信验证码
            if ($data['scene'] == LoginEnum::MOBILE_LOGIN) {
                $result = (new SmsDriver())->verify($data['mobile'], $data['code'], NoticeEnum::REGISTER_CAPTCHA);
                if (!$result) {
                    return '验证码错误';
                }
            }
            //邮箱验证码
            if ($data['scene'] == LoginEnum::EMAIL_LOGIN) {
                $result = (new EmailService())->verify($data['email'], $data['code'], NoticeEnum::REGISTER_CAPTCHA);
                if (!$result) {
                    return '验证码错误';
                }
            }
        }

        return true;
    }


    /**
     * @notes 校验手机号
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2023/7/27 10:23 上午
     */
    public function checkMobile($value, $rule, $data)
    {
        $user = User::where(['mobile'=>$value])->findOrEmpty();
        if (!$user->isEmpty()) {
            return '手机号已被注册，请重新输入';
        }

        return true;
    }


    /**
     * @notes 校验邮箱
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2023/7/27 10:23 上午
     */
    public function checkEmail($value, $rule, $data)
    {
        $user = User::where(['email'=>$value])->findOrEmpty();
        if (!$user->isEmpty()) {
            return '邮箱已被注册，请重新输入';
        }

        return true;
    }
}
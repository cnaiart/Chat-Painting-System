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
namespace app\common\enum;

/**
 * 登录枚举
 * Class LoginEnum
 * @package app\common\enum
 */
class LoginEnum
{
    /**
     * 支持的登录方式
     * WECHAT_LOGIN 微信登录/公众号授权登录
     * MOBILE_LOGIN 手机号登录
     * EMAIL_LOGIN 邮箱登录
     * MOBILE_CODE_LOGIN 手机号验证码登录
     */
    const WECHAT_LOGIN = 1;
    const MOBILE_LOGIN = 2;
    const EMAIL_LOGIN = 3;
    const MOBILE_CODE_LOGIN = 4;
}
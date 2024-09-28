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
namespace app\adminapi\validate\user;


use app\common\model\user\User;
use app\common\validate\BaseValidate;

/**
 * 用户验证
 * Class UserValidate
 * @package app\adminapi\validate\user
 */
class UserValidate extends BaseValidate
{
    protected $regex = [
        'password' => '/^(?![0-9]+$)(?![a-z]+$)(?![A-Z]+$)(?!([^(0-9a-zA-Z)]|[\(\)])+$)([^(0-9a-zA-Z)]|[\(\)]|[a-z]|[A-Z]|[0-9]){6,20}$/'
    ];

    protected $rule = [
        'id' => 'require|checkUser',
        'field' => 'require|checkField',
        'value' => 'require',
//        'member_end_time' => 'date',
        'member_perpetual' => 'require|in:0,1',
        'password' => 'require|length:6,20|regex:password',
        'avatar' => 'require',
        'nickname' => 'require',
        'mobile' => 'requireWithout:email|mobile',
        'email' => 'requireWithout:mobile|email',
        'password_confirm' => 'require|confirm:password',
        'user_member_id' => 'require',
        'sort' => 'require|number',
        'adjust_type' => 'require|in:1,2',
        'leader_id' => 'requireIf:adjust_type,1',
    ];

    protected $message = [
        'id.require' => '请选择用户',
        'field.require' => '请选择操作',
        'value.require' => '请输入内容',
//        'member_end_time.date' => '会员到期时间格式错误',
        'member_perpetual.require' => '请选择是否永久',
        'member_perpetual.in' => '永久值错误',
        'password.require' => '请输入登录密码',
        'password.length' => '登录密码须在6-20位之间',
        'password.regex' => '登录密码须为数字,字母或符号组合',
        'avatar.require' => '请选择用户头像',
        'nickname.require' => '请输入用户昵称',
        'mobile.requireWithout' => '手机号和邮箱必填一个',
        'email.requireWithout' => '手机号和邮箱必填一个',
        'password_confirm.require' => '请输入确认密码',
        'password_confirm.confirm' => '两次输入的密码不一致',
        'user_member_id.require' => '参数缺失',
        'sort.require' => '请输入排序值',
        'sort.number' => '排序值错误',
        'adjust_type.require' => '请选择调整方式',
        'adjust_type.in' => '调整方式值错误',
        'leader_id.requireIf' => '选择邀请人',
    ];


    public function sceneDetail()
    {
        return $this->only(['id']);
    }

    public function sceneAdjustMember()
    {
        return $this->only(['id','member_perpetual']);
    }

    public function sceneEdit()
    {
        return $this->only(['id','field','value']);
    }

    public function sceneBlacklist()
    {
        return $this->only(['id']);
    }

    public function sceneRePassword()
    {
        return $this->only(['id','password']);
    }

    public function sceneAdd()
    {
        return $this->only(['avatar','nickname','mobile','email','password','password_confirm']);
    }

    public function sceneUserMemberSort()
    {
        return $this->only(['user_member_id','sort']);
    }

    public function sceneAdjustLeader()
    {
        return $this->only(['id','adjust_type','leader_id']);
    }


    /**
     * @notes 用户信息校验
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 段誉
     * @date 2022/9/22 17:03
     */
    public function checkUser($value, $rule, $data)
    {
        $userIds = is_array($value) ? $value : [$value];

        foreach ($userIds as $item) {
            if (!User::find($item)) {
                return '用户不存在！';
            }
        }
        return true;
    }


    /**
     * @notes 校验是否可更新信息
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author 段誉
     * @date 2022/9/22 16:37
     */
    public function checkField($value, $rule, $data)
    {
        $allowField = ['sex', 'mobile', 'real_name', 'email'];

        if (!in_array($value, $allowField)) {
            return '用户信息不允许更新';
        }

        switch ($value) {
            case 'account':
                //验证手机号码是否存在
                $account = User::where([
                    ['id', '<>', $data['id']],
                    ['account', '=', $data['value']]
                ])->findOrEmpty();

                if (!$account->isEmpty()) {
                    return '账号已被使用';
                }
                break;

            case 'mobile':
                if (false == $this->validate($data['value'], 'mobile', $data)) {
                    return '手机号码格式错误';
                }

                //验证手机号码是否存在
                $mobile = User::where([
                    ['id', '<>', $data['id']],
                    ['mobile', '=', $data['value']]
                ])->findOrEmpty();

                if (!$mobile->isEmpty()) {
                    return '手机号码已存在';
                }
                break;

            case 'email':
                if (false == $this->validate($data['value'], 'email', $data)) {
                    return '邮箱格式错误';
                }

                //验证邮箱是否存在
                $email = User::where([
                    ['id', '<>', $data['id']],
                    ['email', '=', $data['value']]
                ])->findOrEmpty();

                if (!$email->isEmpty()) {
                    return '邮箱已存在';
                }
                break;
        }
        return true;
    }


}
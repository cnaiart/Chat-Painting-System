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
namespace app\adminapi\logic\user;

use app\common\enum\MemberPackageEnum;
use app\common\enum\PayEnum;
use app\common\enum\user\AccountLogEnum;
use app\common\enum\user\UserTerminalEnum;
use app\common\logic\AccountLogLogic;
use app\common\logic\BaseLogic;
use app\common\model\member\MemberPackage;
use app\common\model\user\User;
use app\common\model\user\UserMember;
use app\common\service\FileService;
use app\common\service\user\UserService;
use Exception;
use think\facade\Config;
use think\facade\Db;

/**
 * 用户逻辑层
 * Class UserLogic
 * @package app\adminapi\logic\user
 */
class UserLogic extends BaseLogic
{

    /**
     * @notes 用户详情
     * @param int $userId
     * @return array
     * @author 段誉
     * @date 2022/9/22 16:32
     */
    public static function detail(int $userId): array
    {
        $user = User::where(['id' => $userId])
            ->withoutField('password,update_time,delete_time')
            ->append(['channel_desc','inviter_name','invite_num','member_info'])
            ->findOrEmpty()
            ->toArray();

        //会员
        $user['member_desc'] = $user['member_info']['member_name'];
        $user['member_end_time_desc'] = $user['member_info']['member_end_time'];
        $user['is_member'] = $user['member_info']['is_member'];
        $user['member_package_id'] = $user['member_info']['member_package_id'];

        //分销
        $user['is_distribution_desc'] = '未开通';
        if ($user['is_distribution']) {
            $user['is_distribution_desc'] = '已开通';
        }

        return $user;
    }


    /**
     * @notes 更新用户信息
     * @param array $params
     * @return User
     * @author 段誉
     * @date 2022/9/22 16:38
     */
    public static function setUserInfo(array $params)
    {
        return User::update([
            'id' => $params['id'],
            $params['field'] => $params['value']
        ]);
    }


    /**
     * @notes 调整用户余额
     * @param array $params
     * @return bool|string
     * @author 段誉
     * @date 2023/2/23 14:25
     */
    public static function adjustUserMoney(array $params)
    {
        Db::startTrans();
        try {
            $user = User::find($params['user_id']);
            if (AccountLogEnum::INC == $params['action']) {
                //调整余额
                $user->balance += $params['num'];
                $user->save();
                //记录日志
                AccountLogLogic::add(
                    $user->id,
                    AccountLogEnum::UM_INC_ADMIN,
                    AccountLogEnum::INC,
                    $params['num'],
                    '',
                    $params['remark'] ?? ''
                );
            } else {
                $user->balance -= $params['num'];
                $user->save();
                //记录日志
                AccountLogLogic::add(
                    $user->id,
                    AccountLogEnum::UM_DEC_ADMIN,
                    AccountLogEnum::DEC,
                    $params['num'],
                    '',
                    $params['remark'] ?? ''
                );
            }

            Db::commit();
            return true;

        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
    }

    /**
     * @notes 调整会员到期时间
     * @param array $params
     * @return bool
     * @author ljj
     * @date 2023/4/14 4:11 下午
     */
    public static function adjustMember(array $params)
    {
        if ($params['member_perpetual']) {
            User::update([
                'id' => $params['id'],
                'member_perpetual' => 1
            ]);
        } else {
            User::update([
                'id' => $params['id'],
                'member_end_time' => empty($params['member_end_time']) ? null : strtotime($params['member_end_time']),
                'member_perpetual' => 0
            ]);
        }

        if ((isset($params['member_end_time']) && !empty($params['member_end_time'])) || $params['member_perpetual']) {
            if (!isset($params['member_package_id']) || empty($params['member_package_id'])) {
                return '请选择会员套餐';
            }
        }

        //添加会员开通记录
        $member_package = MemberPackage::where(['id'=>$params['member_package_id'] ?? 0])->findOrEmpty()->toArray();
        UserMember::create([
            'user_id' => $params['id'],
            'operate_id' => $params['admin_id'],
            'channel' => MemberPackageEnum::CHANNEL_ADMIN,
            'package_name' => $member_package['name'] ?? '',
            'member_end_time' => (isset($params['member_package_id']) && !empty($params['member_package_id'])) ? ((isset($params['member_end_time']) && $params['member_end_time'] != '') ? strtotime($params['member_end_time']) : null) : null,
            'is_perpetual' => $params['member_perpetual'],
            'package_info' => json_encode($member_package,JSON_UNESCAPED_UNICODE),
            'add_member_time' => (isset($params['member_end_time']) && $params['member_end_time'] != '') ? (strtotime($params['member_end_time']) - time()) : 0,
        ]);

        return true;
    }


    /**
     * @notes 用户放入黑名单
     * @param array $params
     * @return bool
     * @author cjhao
     * @date 2023/5/26 10:40
     */
    public static function blacklist(array $params)
    {
        $user = User::findOrEmpty($params['id']);
        $user->is_blacklist = $user->is_blacklist ? 0 : 1;
        $user->save();
        return true;
    }

    /**
     * @notes 调整绘画次数
     * @param array $params
     * @return bool|string
     * @author 段誉
     * @date 2023/6/28 15:57
     */
    public static function adjustUserDraw(array $params)
    {
        Db::startTrans();
        try {
            $user = User::find($params['user_id']);
            if (AccountLogEnum::INC == $params['action']) {
                //调整余额
                $user->balance_draw += $params['num'];
                $user->save();
                //记录日志
                AccountLogLogic::add(
                    $user->id,
                    AccountLogEnum::DRAW_INC_ADMIN,
                    AccountLogEnum::INC,
                    $params['num'],
                    '',
                    $params['remark'] ?? ''
                );
            } else {
                $user->balance_draw -= $params['num'];
                $user->save();
                //记录日志
                AccountLogLogic::add(
                    $user->id,
                    AccountLogEnum::DRAW_DEC_ADMIN,
                    AccountLogEnum::DEC,
                    $params['num'],
                    '',
                    $params['remark'] ?? ''
                );
            }

            Db::commit();
            return true;

        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
    }

    /**
     * @notes 重置密码
     * @param $params
     * @return bool
     * @author ljj
     * @date 2023/8/29 4:42 下午
     */
    public static function rePassword($params)
    {
        $passwordSalt = Config::get('project.unique_identification');
        $password = create_password($params['password'], $passwordSalt);

        User::update([
            'password' => $password,
        ],['id'=>$params['id']]);

        return true;
    }

    /**
     * @notes 新增用户
     * @param $params
     * @return bool
     * @author ljj
     * @date 2023/10/7 2:57 下午
     */
    public static function add($params)
    {
        try {
            $userSn = User::createUserSn();
            $passwordSalt = Config::get('project.unique_identification');
            $password = create_password($params['password'], $passwordSalt);

            $modelUser = new User();
            if (!empty($params['mobile'])) {
                $isMobile = $modelUser->where(['mobile' => $params['mobile']])->findOrEmpty();
                if (!$isMobile->isEmpty()) {
                    throw new Exception('手机已被占用,换一个吧！');
                }
            }

            if (!empty($params['email'])) {
                $isEmail = $modelUser->where(['email' => $params['email']])->findOrEmpty();
                if (!$isEmail->isEmpty()) {
                    throw new Exception('邮箱已被占用,换一个吧！');
                }
            }

            $user = User::create([
                'sn'           => $userSn,
                'avatar'       => FileService::setFileUrl($params['avatar']),
                'nickname'     => $params['nickname'],
                'mobile'       => $params['mobile']??'',
                'email'        => $params['email']??'',
                'real_name'    => $params['real_name']??'',
                'account'      => 'u'.$userSn,
                'password'     => $password,
                'channel'      => UserTerminalEnum::ADMIN,
            ]);

            //注册事件
            (new UserService())->registerEvent($user->id);
            return true;
        } catch (Exception $e) {
            self::$error = $e->getMessage();
            return false;
        }
    }


    /**
     * @notes 调整会员开通记录排序
     * @param array $params
     * @return bool
     * @author ljj
     * @date 2023/11/2 11:34 上午
     */
    public static function userMemberSort(array $params)
    {
        UserMember::update(['sort' => $params['sort'],],['id'=>$params['user_member_id']]);

        return true;
    }

    /**
     * @notes 调整邀请人
     * @param array $params
     * @return bool|string
     * @author ljj
     * @date 2024/1/10 11:48 上午
     */
    public static function adjustLeader(array $params)
    {
        Db::startTrans();
        try {
            switch ($params['adjust_type']) {
                case 1://指定邀请人
                    if ($params['leader_id'] == $params['id']) {
                        throw new \Exception('不能成为自己的邀请人');
                    }

                    $leader = User::where(['id'=>$params['leader_id']])->findOrEmpty()->toArray();
                    $first_leader = $params['leader_id'];
                    $second_leader = $leader['first_leader'];
                    break;
                case 2://设置邀请人为系统
                    $first_leader = 0;
                    $second_leader = 0;
                    break;
            }

            //绑定新上级关系
            User::update(['inviter_id'=>$first_leader,'first_leader'=>$first_leader,'second_leader'=>$second_leader],['id'=>$params['id']]);

            //更新用户下级的分销关系
            User::update(['second_leader'=>$first_leader],['first_leader'=>$params['id']]);

            // 提交事务
            Db::commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return $e->getMessage();
        }
    }
}
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


namespace app\common\model\user;


use app\common\enum\PayEnum;
use app\common\enum\user\UserEnum;
use app\common\enum\user\UserTerminalEnum;
use app\common\model\BaseModel;
use app\common\model\member\MemberOrder;
use app\common\model\member\MemberPackage;
use app\common\model\recharge\RechargeOrder;
use app\common\service\FileService;
use think\model\concern\SoftDelete;

/**
 * 用户模型
 * Class User
 * @package app\common\model\user
 */
class User extends BaseModel
{
    use SoftDelete;

    protected $deleteTime = 'delete_time';


    /**
     * @notes 关联用户授权模型
     * @return \think\model\relation\HasOne
     * @author 段誉
     * @date 2022/9/22 16:03
     */
    public function userAuth()
    {
        return $this->hasOne(UserAuth::class, 'user_id');
    }


    /**
     * @notes 搜索器-用户信息
     * @param $query
     * @param $value
     * @param $data
     * @author 段誉
     * @date 2022/9/22 16:12
     */
    public function searchKeywordAttr($query, $value, $data)
    {
        if ($value) {
            $query->where('sn|nickname|mobile|email', 'like', '%' . $value . '%');
        }
    }


    /**
     * @notes 搜索器-注册来源
     * @param $query
     * @param $value
     * @param $data
     * @author 段誉
     * @date 2022/9/22 16:13
     */
    public function searchChannelAttr($query, $value, $data)
    {
        if ($value) {
            $query->where('channel', '=', $value);
        }
    }


    /**
     * @notes 搜索器-注册时间
     * @param $query
     * @param $value
     * @param $data
     * @author 段誉
     * @date 2022/9/22 16:13
     */
    public function searchCreateTimeStartAttr($query, $value, $data)
    {
        if ($value) {
            $query->where('create_time', '>=', strtotime($value));
        }
    }


    /**
     * @notes 搜索器-注册时间
     * @param $query
     * @param $value
     * @param $data
     * @author 段誉
     * @date 2022/9/22 16:13
     */
    public function searchCreateTimeEndAttr($query, $value, $data)
    {
        if ($value) {
            $query->where('create_time', '<=', strtotime($value));
        }
    }


    /**
     * @notes 搜索器-是否分销商
     * @param $query
     * @param $value
     * @param $data
     * @author ljj
     * @date 2023/5/24 10:53 上午
     */
    public function searchIsDistributionAttr($query, $value, $data)
    {
        if (isset($value) && $value != '') {
            $query->where('is_distribution', '=', strtotime($value));
        }
    }


    /**
     * @notes 搜索器-是否是会员
     * @param $query
     * @param $value
     * @param $data
     * @author ljj
     * @date 2023/5/24 10:53 上午
     */
    public function searchIsMemberAttr($query, $value, $data)
    {
        if (isset($value) && $value == 1) {
            $query->whereNotNull('member_end_time')->whereOr('member_perpetual', '=', 1);
        }
        if (isset($value) && $value == 0) {
            $query->whereNull('member_end_time')->where('member_perpetual', '=', 0);
        }
    }


    /**
     * @notes 头像获取器 - 用于头像地址拼接域名
     * @param $value
     * @return string
     * @author Tab
     * @date 2021/7/17 14:28
     */
    public function getAvatarAttr($value)
    {
        return trim($value) ? FileService::getFileUrl($value) : '';
    }


    /**
     * @notes 获取器-性别描述
     * @param $value
     * @param $data
     * @return string|string[]
     * @author 段誉
     * @date 2022/9/7 15:15
     */
    public function getSexAttr($value, $data)
    {
        return UserEnum::getSexDesc($value);
    }


    /**
     * @notes 登录时间
     * @param $value
     * @return string
     * @author 段誉
     * @date 2022/9/23 18:15
     */
    public function getLoginTimeAttr($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : '';
    }

    /**
     * @notes 生成用户编码
     * @param string $prefix
     * @param int $length
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 段誉
     * @date 2022/9/16 10:33
     */
    public static function createUserSn($prefix = '', $length = 8)
    {
        $rand_str = '';
        for ($i = 0; $i < $length; $i++) {
            $rand_str .= mt_rand(0, 9);
        }
        $sn = $prefix . $rand_str;
        if (User::where(['sn' => $sn])->find()) {
            return self::createUserSn($prefix, $length);
        }
        return $sn;
    }

    /**
     * @notes 注册来源
     * @param $value
     * @param $data
     * @return array|mixed|string|string[]
     * @author ljj
     * @date 2023/4/17 10:21 上午
     */
    public function getChannelDescAttr($value,$data)
    {
        return UserTerminalEnum::getTermInalDesc($data['channel']);
    }

    /**
     * @notes 订单数量
     * @param $value
     * @param $data
     * @return int
     * @author ljj
     * @date 2023/5/25 10:02 上午
     */
    public function getOrderNumAttr($value,$data)
    {
        $member_num = MemberOrder::where(['user_id'=>$data['id'],'pay_status'=>PayEnum::ISPAID])->count();
        $recharge_num = RechargeOrder::where(['user_id'=>$data['id'],'pay_status'=>PayEnum::ISPAID])->count();
        return $member_num + $recharge_num;
    }

    /**
     * @notes 邀请人数
     * @param $value
     * @param $data
     * @return int
     * @author ljj
     * @date 2023/5/25 10:03 上午
     */
    public function getInviteNumAttr($value,$data)
    {
        return User::where(['inviter_id'=>$data['id']])->count();
    }

    /**
     * @notes 邀请人昵称
     * @param $value
     * @param $data
     * @return mixed
     * @author ljj
     * @date 2023/5/26 11:08 上午
     */
    public function getInviterNameAttr($value,$data)
    {
        return User::where(['id'=>$data['inviter_id']])->value('nickname');
    }

    /**
     * @notes 会员信息
     * @param $value
     * @param $data
     * @return array
     * @author ljj
     * @date 2023/8/7 12:08 下午
     */
    public function getMemberInfoAttr($value,$data)
    {
        $result['member_name'] = '未开通';
        $result['member_end_time'] = '-';
        $result['is_end'] = 0;
        $result['is_member'] = 0;
        $result['member_package_id'] = 0;
        $result['chat_limit'] = 0;
        if (isset($data['member_perpetual']) && $data['member_perpetual']) {
            $result['member_name'] = '已开通';
            $result['member_end_time'] = '永久';
            $result['is_member'] = 1;
        } elseif ($data['member_end_time']) {
            $result['member_name'] = '已开通';
            $result['member_end_time'] = date('Y-m-d H:i:s',$data['member_end_time']);
            if ($data['member_end_time'] < time()) {
                $result['is_end'] = 1;
            }
            $result['is_member'] = 1;
        }
        $user_member = UserMember::field('package_name,package_info')
            ->where(['user_id'=>$data['id'],'refund_status'=>PayEnum::REFUND_NOT])
            ->order(['sort'=>'desc','is_perpetual'=>'desc','add_member_time'=>'desc','id'=>'desc'])
            ->json(['package_info'],true)
            ->findOrEmpty()
            ->toArray();
        if (!empty($user_member) && (!empty($data['member_end_time']) || $data['member_perpetual'])) {
            $result['member_package_id'] = $user_member['package_info']['id'] ?? 0;
            $member_package = MemberPackage::where(['id'=>$result['member_package_id']])->field('name,chat_limit')->findOrEmpty()->toArray();
            if (!empty($member_package)) {
                $result['member_name'] = $member_package['name'];
                $result['chat_limit'] = $member_package['chat_limit'];
            }
        }

        return $result;
    }
}
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


use app\common\enum\ChatRecordEnum;
use app\common\enum\YesNoEnum;
use app\common\model\ChatCategory;
use app\common\model\user\User;
use app\common\validate\BaseValidate;

class ChatRecordsValidate extends BaseValidate
{
    protected $rule = [
        'user_id'       => 'require',
        'category_id'   => 'requireIf:type,'.ChatRecordEnum::CHAT_QUESTION.'|checkCategory',
        'type'          => 'require|in:'.ChatRecordEnum::CHAT_QUESTION.','.ChatRecordEnum::CHAT_SKILL.','.ChatRecordEnum::CHAT_CREATION.','.ChatRecordEnum::CHAT_MIND,
        'records_id'    => 'require',
        'collect_id'    => 'require',
    ];

    protected $message = [
        'user_id.require'           => '参数缺失',
        'category_id.requireIf'     => '请选择对话',
        'type.require'              => '参数缺失',
        'type.in'                   => '参数错误',
        'records_id.require'        => '参数缺失',
        'collect_id.require'        => '参数缺失',
    ];


    public function sceneCleanChat()
    {
        return $this->only(['user_id','type']);
    }

    public function sceneCollect()
    {
        return $this->only(['user_id','records_id']);
    }

    public function sceneCancelCollect()
    {
        return $this->only(['user_id','collect_id']);
    }

    public function sceneChatLog()
    {
        return $this->only(['user_id','type']);
    }


    /**
     * @notes 校验会员
     * @param $value
     * @param $rule
     * @param $data
     * @throws \think\Exception
     * @author ljj
     * @date 2023/4/25 11:17 上午
     */
    public function checkUser($value,$rule,$data)
    {
        $user = User::where('id',$value)->findOrEmpty()->toArray();
        if (empty($user)) {
            return '非法会员';
        }
        if(YesNoEnum::YES == $user['is_blacklist']){
            return '您已被管理员禁止提问，请联系客服详询原因。';
        }
        if (!isset($user['member_perpetual']) || !$user['member_perpetual']) {
            if (!$user['member_end_time'] || $user['member_end_time'] < time()) {
                if ($user['balance'] < 1) {
                    return '会员余额不足';
                }
            }
        }
        return true;
    }

    /**
     * @notes 验证会话分类
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author cjhao
     * @date 2023/6/9 14:51
     */
    public function checkCategory($value,$rule,$data){
        if(ChatRecordEnum::CHAT_QUESTION != $data['type']){
            return true;
        }
        $chatCategory = ChatCategory::where(['id' => $value, 'user_id' => $data['user_id']])->findOrEmpty();
        if($chatCategory->isEmpty()){
            return '会话不存在';
        }
        return true;
    }
}
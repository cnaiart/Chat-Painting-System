<?php
// +----------------------------------------------------------------------
// | likeshop100%开源免费商用商城系统
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | 商业版本务必购买商业授权，以免引起法律纠纷
// | 禁止对系统程序代码以任何目的，任何形式的再发布
// | gitee下载：https://gitee.com/likeshop_gitee
// | github下载：https://github.com/likeshop-github
// | 访问官网：https://www.likeshop.cn
// | 访问社区：https://home.likeshop.cn
// | 访问手册：http://doc.likeshop.cn
// | 微信公众号：likeshop技术社区
// | likeshop团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeshopTeam
// +----------------------------------------------------------------------
namespace app\api\validate;
use app\common\enum\ChatRecordEnum;
use app\common\enum\YesNoEnum;
use app\common\model\ChatCategory;
use app\common\model\user\User;
use app\common\validate\BaseValidate;

/**
 * 对话验证器类
 * Class ChatValidate
 * @package app\api\validate
 */
class ChatValidate extends BaseValidate
{
    protected $rule = [
        'user_id'       => 'require',
        'category_id'   => 'checkCategory',
        'question'      => 'require',
        'type'          => 'require|in:'.ChatRecordEnum::CHAT_QUESTION.','.ChatRecordEnum::CHAT_CREATION.','.ChatRecordEnum::CHAT_SKILL,
        'other_id'      => 'checkOtherId',
        'form'          => 'requireWithout:question|array'
    ];

    protected $message = [
        'user_id.require'           => '参数缺失',
        'question.require'          => '请输入内容',
        'other_id.require'          => '请选择创作模型',
        'form.requireWithout'              => '请输入内容',
        'form.array'                => '内容数据错误',
    ];


    protected function sceneChat()
    {
        return $this->remove(['id'=>true,'form'=>true]);
    }

    protected function sceneCreationChat()
    {
        return $this->remove(['question'=>true,'category_id'=>true]);
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
        if($user['member_perpetual']){
            return true;
        }
        if (!$user['member_end_time'] || $user['member_end_time'] < time()) {
            if ($user['balance'] < 1) {
                return '会员余额不足';
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
        if(empty($value)){
            return '请选择会话';
        }
        $chatCategory = ChatCategory::where(['id' => $value, 'user_id' => $data['user_id']])->findOrEmpty();
        if($chatCategory->isEmpty()){
            return '会话不存在';
        }
        return true;
    }

    public function checkOtherId($value,$rule,$data)
    {
        if(ChatRecordEnum::CHAT_QUESTION != $data['type']){
            return true;
        }
        if(empty($value)){
            return '请选择模型';
        }
        return true;

    }


}
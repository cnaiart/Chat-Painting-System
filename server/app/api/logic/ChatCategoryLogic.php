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
namespace app\api\logic;
use app\common\model\ChatCategory;

/**
 * 对话分类逻辑类
 * Class ChatCategoryLogic
 * @package app\api\logic
 */
class ChatCategoryLogic
{

    /**
     * @notes 添加对话分类
     * @return bool
     * @author cjhao
     * @date 2023/5/30 16:47
     */
    public function add($userId)
    {
        $chatCategory = new ChatCategory();
        $chatCategory->name = "新的会话";
        $chatCategory->user_id = $userId;
        $chatCategory->save();
        return true;
    }

    /**
     * @notes 编辑分类
     * @param array $post
     * @author cjhao
     * @date 2023/5/30 18:02
     */
    public function edit(array $post)
    {
        $chatCategory = ChatCategory::where(['id' => $post['id'], 'user_id' => $post['user_id']])
            ->findOrEmpty();
        if($chatCategory->isEmpty()){
            return '对话不存在';
        }
        $chatCategory->name = mb_substr($post['name'],0,20,"utf-8");
        $chatCategory->save();
        return true;
    }


    /**
     * @notes 删除分类
     * @param array $post
     * @return bool
     * @author cjhao
     * @date 2023/5/31 11:09
     */
    public function del(array $post)
    {
        ChatCategory::where(['user_id'=>$post['user_id'],'id'=>$post['id']])->delete();
        $count = ChatCategory::where(['user_id'=>$post['user_id']])->count();
        if( 0 == $count){
            //创建一个默认会话
            ChatCategory::create(['name'=>'新的会话','user_id'=>$post['user_id']]);
        }
        return true;
    }


    /**
     * @notes 清理会话
     * @param $userId
     * @author cjhao
     * @date 2023/5/31 15:11
     */
    public function clear($userId)
    {
        ChatCategory::where(['user_id'=>$userId])->delete();
        //创建一个默认会话
        ChatCategory::create(['name'=>'新的会话','user_id'=>$userId]);
    }

}
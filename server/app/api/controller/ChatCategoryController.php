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
namespace app\api\controller;
use app\api\logic\ChatCategoryLogic;
use app\api\validate\ChatCategoryValidate;

/**
 * 对话分类
 * Class ChatCategory
 * @package app\api\controller
 */
class ChatCategoryController extends BaseApiController
{


    public function lists()
    {
        return $this->dataLists();
    }


    /**
     * @notes 添加分类
     * @author cjhao
     * @date 2023/5/30 16:44
     */
    public function add()
    {
        (new ChatCategoryLogic())->add($this->userId);
        return $this->success();
    }

    /**
     * @notes 编辑分类
     * @author cjhao
     * @date 2023/5/30 18:01
     */
    public function edit()
    {
        $post = (new ChatCategoryValidate())->post()->goCheck();
        $post['user_id'] = $this->userId;
        $result = (new ChatCategoryLogic())->edit($post);
        if(true === $result){
            return $this->success('修改成功');
        }
        return $this->fail($result);
    }


    /**
     * @notes 删除会话分类
     * @return mixed
     * @author cjhao
     * @date 2023/5/31 11:08
     */
    public function del()
    {
        $post = (new ChatCategoryValidate())->post()->goCheck('id');
        $post['user_id'] = $this->userId;
        (new ChatCategoryLogic())->del($post);
        return $this->success('删除成功',[],1,1);
    }


    /**
     * @notes 清除会话分类
     * @return mixed
     * @author cjhao
     * @date 2023/5/31 15:02
     */
    public function clear()
    {
        (new ChatCategoryLogic())->clear($this->userId);
        return $this->success('清除成功',[],1,1);

    }

}
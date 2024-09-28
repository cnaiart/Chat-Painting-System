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
namespace app\adminapi\controller\question;
use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\question\QuestionCategoryLogic;
use app\adminapi\validate\question\QuestionCategoryValidate;

/**
 * 示例分类控制器类
 * Class QuestionCategory
 * @package app\adminapi\controller\question
 */
class QuestionCategoryController extends BaseAdminController
{

    /**
     * @notes 列表
     * @return \think\response\Json
     * @author cjhao
     * @date 2023/4/14 11:44
     */
    public function lists()
    {
        return $this->dataLists();
    }


    /**
     * @notes 分类
     * @return \think\response\Json
     * @author cjhao
     * @date 2023/4/14 15:23
     */
    public function add()
    {
        $post = (new QuestionCategoryValidate())->post()->goCheck('add');
        (new QuestionCategoryLogic())->add($post);
        return $this->success('添加成功',[],1,1);
    }

    /**
     * @notes 修改
     * @return \think\response\Json
     * @author cjhao
     * @date 2023/4/14 15:24
     */
    public function edit()
    {
        $post = (new QuestionCategoryValidate())->post()->goCheck();
        (new QuestionCategoryLogic())->edit($post);
        return $this->success('修改成功',[],1,1);

    }

    /**
     * @notes 获取详情
     * @return \think\response\Json
     * @author cjhao
     * @date 2023/4/17 10:08
     */
    public function detail()
    {
        $post = (new QuestionCategoryValidate())->goCheck('id');
        $detail = (new QuestionCategoryLogic())->detail($post['id']);
        return $this->success('',$detail);
    }

    /**
     * @notes 删除
     * @return \think\response\Json
     * @author cjhao
     * @date 2023/4/14 15:27
     */
    public function del()
    {
        $post = (new QuestionCategoryValidate())->post()->goCheck('id');
        (new QuestionCategoryLogic())->del($post['id']);
        return $this->success('删除成功',[],1,1);
    }


    /**
     * @notes 状态修改成功
     * @return \think\response\Json
     * @author cjhao
     * @date 2023/4/14 15:29
     */
    public function status()
    {
        $post = (new QuestionCategoryValidate())->post()->goCheck('id');
        (new QuestionCategoryLogic())->status($post['id']);
        return $this->success('修改成功',[],1,1);
    }

}

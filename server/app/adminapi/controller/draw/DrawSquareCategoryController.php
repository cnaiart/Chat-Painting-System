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

namespace app\adminapi\controller\draw;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\draw\DrawSquareCategoryLists;
use app\adminapi\logic\draw\DrawSquareCategoryLogic;
use app\adminapi\validate\draw\DrawSquareCategoryValidate;


class DrawSquareCategoryController extends BaseAdminController
{
    /**
     * @notes 绘画广场分类列表
     * @return mixed
     * @author ljj
     * @date 2023/8/31 10:57 上午
     */
    public function lists()
    {
        return $this->dataLists(new DrawSquareCategoryLists());
    }

    /**
     * @notes 添加分类
     * @return mixed
     * @author ljj
     * @date 2023/8/31 11:03 上午
     */
    public function add()
    {
        $params = (new DrawSquareCategoryValidate())->post()->goCheck('add');
        DrawSquareCategoryLogic::add($params);
        return $this->success('添加成功', [], 1, 1);
    }

    /**
     * @notes 编辑分类
     * @return mixed
     * @author ljj
     * @date 2023/8/31 11:04 上午
     */
    public function edit()
    {
        $params = (new DrawSquareCategoryValidate())->post()->goCheck('edit');
        DrawSquareCategoryLogic::edit($params);
        return $this->success('编辑成功', [], 1, 1);
    }

    /**
     * @notes 删除分类
     * @return mixed
     * @author ljj
     * @date 2023/8/31 11:05 上午
     */
    public function del()
    {
        $params = (new DrawSquareCategoryValidate())->post()->goCheck('del');
        DrawSquareCategoryLogic::del($params);
        return $this->success('删除成功', [], 1, 1);
    }

    /**
     * @notes 分类详情
     * @return mixed
     * @author ljj
     * @date 2023/8/31 11:08 上午
     */
    public function detail()
    {
        $params = (new DrawSquareCategoryValidate())->goCheck('detail');
        $result = DrawSquareCategoryLogic::detail($params);
        return $this->data($result);
    }

    /**
     * @notes 状态切换
     * @return mixed
     * @author ljj
     * @date 2023/8/31 11:09 上午
     */
    public function status()
    {
        $post = (new DrawSquareCategoryValidate())->post()->goCheck('status');
        DrawSquareCategoryLogic::status($post['id']);
        return $this->success('修改成功', [], 1, 1);
    }
}
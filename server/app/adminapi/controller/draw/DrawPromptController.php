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
use app\adminapi\lists\draw\DrawPromptLists;
use app\adminapi\logic\draw\DrawPromptLogic;
use app\adminapi\validate\draw\DrawPromptValidate;


/**
 * 绘画关键词
 * Class DrawPromptController
 * @package app\adminapi\controller\draw
 */
class DrawPromptController extends BaseAdminController
{
    /**
     * @notes 列表
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 段誉
     * @date 2023/6/14 17:02
     */
    public function lists()
    {
        return $this->dataLists(new DrawPromptLists());
    }

    /**
     * @notes 添加
     * @return mixed
     * @author 段誉
     * @date 2023/6/14 16:18
     */
    public function add()
    {
        $params = (new DrawPromptValidate())->post()->goCheck('add');
        $res = DrawPromptLogic::add($params);
        if (false === $res) {
            return $this->fail(DrawPromptLogic::getError());
        }
        return $this->success('操作成功', [], 1, 1);
    }

    /**
     * @notes 编辑
     * @return mixed
     * @author 段誉
     * @date 2023/6/14 16:18
     */
    public function edit()
    {
        $params = (new DrawPromptValidate())->post()->goCheck('edit');
        DrawPromptLogic::edit($params);
        return $this->success('操作成功', [], 1, 1);
    }

    /**
     * @notes 删除
     * @return mixed
     * @author 段誉
     * @date 2023/6/14 16:18
     */
    public function delete()
    {
        $params = (new DrawPromptValidate())->post()->goCheck('id');
        DrawPromptLogic::delete($params);
        return $this->success('操作成功', [], 1, 1);
    }

    /**
     * @notes 详情
     * @return mixed
     * @author 段誉
     * @date 2023/6/14 16:18
     */
    public function detail()
    {
        $params = (new DrawPromptValidate())->goCheck('id');
        $result = DrawPromptLogic::detail($params);
        return $this->data($result);
    }

    /**
     * @notes 状态切换
     * @return mixed
     * @author 段誉
     * @date 2023/6/15 10:55
     */
    public function status()
    {
        $post = (new DrawPromptValidate())->post()->goCheck('id');
        DrawPromptLogic::status($post['id']);
        return $this->success('操作成功', [], 1, 1);
    }


}
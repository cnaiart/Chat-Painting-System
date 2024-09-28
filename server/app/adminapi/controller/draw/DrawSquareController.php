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
use app\adminapi\lists\draw\DrawSquareLists;
use app\adminapi\logic\draw\DrawSquareLogic;
use app\adminapi\validate\draw\DrawSquareValidate;


class DrawSquareController extends BaseAdminController
{
    /**
     * @notes 绘画广场列表
     * @return mixed
     * @author ljj
     * @date 2023/8/31 12:09 下午
     */
    public function lists()
    {
        return $this->dataLists(new DrawSquareLists());
    }

    /**
     * @notes 添加
     * @return mixed
     * @author ljj
     * @date 2023/8/31 12:15 下午
     */
    public function add()
    {
        $params = (new DrawSquareValidate())->post()->goCheck('add',['admin_id'=>$this->adminId]);
        DrawSquareLogic::add($params);
        return $this->success('操作成功', [], 1, 1);
    }

    /**
     * @notes 编辑
     * @return mixed
     * @author ljj
     * @date 2023/8/31 12:16 下午
     */
    public function edit()
    {
        $params = (new DrawSquareValidate())->post()->goCheck('edit');
        DrawSquareLogic::edit($params);
        return $this->success('操作成功', [], 1, 1);
    }

    /**
     * @notes 详情
     * @return mixed
     * @author ljj
     * @date 2023/8/31 12:16 下午
     */
    public function detail()
    {
        $params = (new DrawSquareValidate())->goCheck('detail');
        $result = DrawSquareLogic::detail($params);
        return $this->data($result);
    }

    /**
     * @notes 删除
     * @return mixed
     * @author ljj
     * @date 2023/8/31 12:17 下午
     */
    public function del()
    {
        $params = (new DrawSquareValidate())->post()->goCheck('del');
        DrawSquareLogic::del($params);
        return $this->success('操作成功', [], 1, 1);
    }

    /**
     * @notes 显示状态
     * @return mixed
     * @author ljj
     * @date 2023/8/31 12:18 下午
     */
    public function isShow()
    {
        $params = (new DrawSquareValidate())->post()->goCheck('isShow');
        DrawSquareLogic::isShow($params['id']);
        return $this->success('操作成功', [], 1, 1);
    }

    /**
     * @notes 审核状态
     * @return mixed
     * @author ljj
     * @date 2023/8/31 12:19 下午
     */
    public function verifyStatus()
    {
        $params = (new DrawSquareValidate())->post()->goCheck('verifyStatus');
        DrawSquareLogic::verifyStatus($params);
        return $this->success('操作成功', [], 1, 1);
    }

    /**
     * @notes 获取绘画广场配置
     * @return mixed
     * @author ljj
     * @date 2023/8/31 2:49 下午
     */
    public function getConfig()
    {
        $result = DrawSquareLogic::getConfig();
        return $this->success('',$result);
    }

    /**
     * @notes 设置绘画广场配置
     * @return mixed
     * @author ljj
     * @date 2023/8/31 2:50 下午
     */
    public function setConfig()
    {
        $params = (new DrawSquareValidate())->post()->goCheck('setConfig');
        DrawSquareLogic::setConfig($params);
        return $this->success('操作成功', [], 1, 1);
    }
}
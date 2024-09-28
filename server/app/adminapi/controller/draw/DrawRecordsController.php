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
use app\adminapi\lists\draw\DrawRecordsLists;
use app\adminapi\logic\draw\DrawRecordsLogic;
use app\common\enum\DrawEnum;

/**
 * 绘图
 * Class DrawRecordsController
 * @package app\adminapi\controller\draw
 */
class DrawRecordsController extends BaseAdminController
{

    /**
     * @notes 列表
     * @return mixed
     * @author 段誉
     * @date 2023/6/20 18:05
     */
    public function lists()
    {
        return $this->dataLists(new DrawRecordsLists());
    }


    /**
     * @notes 详情
     * @return mixed
     * @author 段誉
     * @date 2023/6/14 16:18
     */
    public function detail()
    {
        $id = $this->request->get('id/d');
        $result = DrawRecordsLogic::detail($id);
        return $this->data($result);
    }


    /**
     * @notes 删除
     * @return mixed
     * @author 段誉
     * @date 2023/6/20 21:00
     */
    public function delete()
    {
        $ids = $this->request->post('ids');
        DrawRecordsLogic::delete($ids);
        return $this->success('操作成功');
    }

    /**
     * @notes 绘画模型
     * @return mixed
     * @author mjf
     * @date 2023/11/6 11:01
     */
    public function drawModel()
    {
        return $this->data(DrawEnum::getAiModelName(true));
    }

}
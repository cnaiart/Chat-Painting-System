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

use app\api\lists\QrcodeRecordsLists;
use app\api\logic\QrcodeRecordsLogic;

/**
 * 艺术二维码
 * Class QrcodeRecordsController
 * @package app\api\controller
 */
class QrcodeRecordsController extends BaseApiController
{

    /**
     * @notes 记录列表
     * @return \think\response\Json
     * @author mjf
     * @date 2023/10/17 18:01
     */
    public function records()
    {
        return $this->dataLists(new QrcodeRecordsLists());
    }

    /**
     * @notes 删除记录
     * @return \think\response\Json
     * @author mjf
     * @date 2023/10/17 18:04
     */
    public function delete()
    {
        $ids = $this->request->post('ids');
        QrcodeRecordsLogic::delete($this->userId, $ids);
        return $this->success();
    }

    /**
     * @notes 详情
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author mjf
     * @date 2023/10/18 16:36
     */
    public function detail()
    {
        $params = $this->request->post();
        $result = QrcodeRecordsLogic::getQrcodeDetail($params, $this->userId);
        return $this->data($result);
    }


}
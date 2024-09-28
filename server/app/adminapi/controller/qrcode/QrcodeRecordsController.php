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

namespace app\adminapi\controller\qrcode;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\qrcode\QrcodeRecordsLists;
use app\adminapi\logic\qrcode\QrcodeRecordsLogic;
use think\response\Json;

/**
 * 艺术二维码
 * Class QrcodeRecordsController
 * @package app\adminapi\controller\qrcode
 */
class QrcodeRecordsController extends BaseAdminController
{

    /**
     * @notes 列表
     * @return Json
     * @author mjf
     * @date 2023/10/17 10:47
     */
    public function lists():Json
    {
        return $this->dataLists(new QrcodeRecordsLists());
    }

    /**
     * @notes 删除
     * @return mixed
     * @author mjf
     * @date 2023/10/17 10:48
     */
    public function delete()
    {
        $ids = $this->request->post('id/a');
        QrcodeRecordsLogic::delete($ids);
        return $this->success('操作成功');
    }

    /**
     * @notes 下拉选项
     * @return mixed
     * @author mjf
     * @date 2023/10/17 15:20
     */
    public function option()
    {
        $result = QrcodeRecordsLogic::option();
        return $this->data($result);
    }

    /**
     * @notes 示例配置
     * @return mixed
     * @author mjf
     * @date 2023/10/17 15:42
     */
    public function getExample()
    {
        $result = QrcodeRecordsLogic::getExample();
        return $this->data($result);
    }

    /**
     * @notes 设置示例
     * @return mixed
     * @author mjf
     * @date 2023/10/17 15:55
     */
    public function setExample()
    {
        $params = $this->request->post();
        $result = QrcodeRecordsLogic::setExample($params);
        if ($result !== true) {
            return $this->fail($result);
        }
        return $this->success('操作成功');
    }

}
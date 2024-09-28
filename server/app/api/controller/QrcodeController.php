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

use app\api\logic\QrcodeLogic;
use app\api\validate\QrcodeValidate;

/**
 * 艺术二维码
 * Class QrcodeController
 * @package app\api\controller
 */
class QrcodeController extends BaseApiController
{

    public array $notNeedLogin = ['notifyMewx', 'notifyZsy', 'config'];

    /**
     * @notes 生成图片
     * @return mixed
     * @author 段誉
     * @date 2023/6/19 20:57
     */
    public function imagine()
    {
        $params = (new QrcodeValidate())->post()->goCheck("imagine");
        $result = QrcodeLogic::imagine($this->userId, $params);
        if (false === $result) {
            return $this->fail(QrcodeLogic::getError());
        }
        return $this->success('', $result);
    }

    /**
     * @notes 模型配置
     * @return \think\response\Json
     * @author mjf
     * @date 2023/10/18 15:16
     */
    public function config()
    {
        $result = QrcodeLogic::config($this->userId);
        return $this->data($result);
    }

    /**
     * @notes 星月熊回调处理
     * @return \think\response\Json
     * @author mjf
     * @date 2023/10/18 15:05
     */
    public function notifyMewx()
    {
        $params = $this->request->post();
        $result = QrcodeLogic::notifyMewx($params);
        if ($result === true) {
            return $this->success('', [], 0);
        }
        return $this->success();
    }

    /**
     * @notes 知数云回调
     * @return \think\response\Json
     * @author mjf
     * @date 2023/11/9 16:33
     */
    public function notifyZsy()
    {
        $params = $this->request->post();
        $result = QrcodeLogic::notifyZsy($params);
        if ($result === true) {
            return $this->success('', [], 0);
        }
        return $this->success();
    }

}
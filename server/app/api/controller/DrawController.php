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

use app\api\logic\DrawLogic;
use app\api\validate\DrawImageValidate;

/**
 * 绘画
 * Class DrawController
 * @package app\api\controller
 */
class DrawController extends BaseApiController
{

    public array $notNeedLogin = ['notifyZsy', 'notifyMdd', 'modelConfig', 'notifyYj', 'getSelector', 'getSdModel'];

    /**
     * @notes 生成图片
     * @return mixed
     * @author 段誉
     * @date 2023/6/19 20:57
     */
    public function imagine()
    {
        $params = (new DrawImageValidate())->post()->goCheck("imagine");
        $result = DrawLogic::imagine($this->userId, $params);
        if (false === $result) {
            return $this->fail(DrawLogic::getError());
        }
        return $this->success('', $result);
    }

    /**
     * @notes 模型配置
     * @return mixed
     * @author 段誉
     * @date 2023/7/19 14:26
     */
    public function modelConfig()
    {
        $result = DrawLogic::modelConfig($this->userId);
        return $this->data($result);
    }


    /**
     * @notes 回调处理
     * @return mixed
     * @author 段誉
     * @date 2023/6/19 20:57
     */
    public function notifyZsy()
    {
        $params = $this->request->post();
        DrawLogic::notifyZsy($params);
        return $this->success();
    }

    /**
     * @notes 回调处理
     * @return mixed
     * @author 段誉
     * @date 2023/6/19 20:57
     */
    public function notifyMdd()
    {
        $params = $this->request->post();
        DrawLogic::notifyMdd($params);
        return $this->success();
    }


    /**
     * @notes 回调处理
     * @return \think\response\Json
     * @author mjf
     * @date 2023/11/6 17:14
     */
    public function notifyYj()
    {
        $params = $this->request->post();
        DrawLogic::notifyYj($params);
        return $this->success();
    }

    /**
     * @notes 意间绘画风格选择
     * @return \think\response\Json
     * @author mjf
     * @date 2023/11/7 17:09\
     */
    public function getSelector()
    {
        $result = DrawLogic::getSelector();
        return $this->success('', $result);
    }

    /**
     * @notes sd模型
     * @return \think\response\Json
     * @author mjf
     * @date 2024/1/4 15:39
     */
    public function getSdModel()
    {
        $result = DrawLogic::getSdModel();
        return $this->success('', $result);
    }

}
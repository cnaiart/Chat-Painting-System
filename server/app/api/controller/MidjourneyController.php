<?php
// +----------------------------------------------------------------------
// | likeadmin快速开发前后端分离管理后台（PHP版）
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | gitee下载：https://gitee.com/likeshop_gitee/likeadmin
// | github下载：https://github.com/likeshop-github/likeadmin
// | 访问官网：https://www.likeadmin.cn
// | likeadmin团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeadminTeam
// +----------------------------------------------------------------------

namespace app\api\controller;

use app\api\logic\MidjourneyLogic;
use app\api\validate\MjChangeValidate;
use app\api\validate\MjImagineValidate;
use think\response\Json;

/**
 * 绘画任务提交
 * Class JourneyController
 * @package app\api\controller
 */
class MidjourneyController extends BaseApiController
{
    public array $notNeedLogin = ['imagine', 'change'];

    /**
     * @notes 文生图,图生图
     * @return Json
     * @author mjf
     * @date 2023/7/28 18:47
     */
    public function imagine(): Json
    {
        $params = (new MjImagineValidate())->post()->goCheck('imagine');
        $result = MidjourneyLogic::imagine($params);
        if (false === $result) {
            return $this->fail(MidjourneyLogic::getError());
        }
        return $this->success('提交成功', $result);
    }

    /**
     * @notes 放大，变换
     * @return Json
     * @author mjf
     * @date 2023/8/1 18:57
     */
    public function change(): Json
    {
        $params = (new MjChangeValidate())->post()->goCheck('change');
        $result = MidjourneyLogic::change($params);
        if (false === $result) {
            return $this->fail(MidjourneyLogic::getError());
        }
        return $this->success('提交成功', $result);
    }

}
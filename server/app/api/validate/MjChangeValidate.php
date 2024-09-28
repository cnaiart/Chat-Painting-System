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

namespace app\api\validate;

use app\common\enum\DrawEnum;
use app\common\validate\BaseValidate;

/**
 * Class MjChangeValidate
 * @package app\api\validate
 */
class MjChangeValidate extends BaseValidate
{

    protected $rule = [
        'action' => 'require|checkAction',
        'task_id' => 'require|integer',
    ];

    protected $message = [
        'action.require' => '绘图操作参数缺失',
        'task_id.require' => '任务id参数缺失',
        'task_id.integer' => '任务id参数异常',
    ];

    public function sceneChange()
    {
        return $this->only(['action', 'task_id']);
    }

    protected function checkAction($value, $rule, $data)
    {
        if (!in_array($value, [DrawEnum::ACTION_VARIATION, DrawEnum::ACTION_UPSAMPLE])) {
            return '绘图操作参数异常';
        }

        return true;
    }

}
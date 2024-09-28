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

namespace app\common\enum;

/**
 * 绘画任务
 * Class DrawEnum
 * @package app\common\enum
 */
class DrawTaskEnum
{
    /**
     * 绘画动作
     * ACTION_GENERATE = 生图
     * ACTION_UPSAMPLE = 放大
     * ACTION_VARIATION = 变换
     */
    const ACTION_GENERATE = "generate";
    const ACTION_UPSAMPLE = "upsample";
    const ACTION_VARIATION = "variation";

    /**
     * 任务状态
     * STATUS_NOT_START = 未处理
     * STATUS_SUBMIT = 已提交
     * STATUS_IN_PROGRESS = 进行中
     * STATUS_FAIL = 失败
     * STATUS_SUCCESS = 成功
     */
    const STATUS_NOT_START = 0;
    const STATUS_SUBMIT = 1;
    const STATUS_IN_PROGRESS = 2;
    const STATUS_SUCCESS = 3;
    const STATUS_FAIL = 4;

    /**
     * @notes 任务状态
     * @param bool $value
     * @return string|string[]
     * @author mjf
     * @date 2023/11/21 10:35
     */
    public static function getStatusDesc($value = true)
    {
        $data = [
            self::STATUS_NOT_START => '未处理',
            self::STATUS_SUBMIT => '已提交',
            self::STATUS_IN_PROGRESS => '执行中',
            self::STATUS_SUCCESS => '成功',
            self::STATUS_FAIL => '失败',
        ];

        if ($value === true) {
            return $data;
        }

        return $data[$value];
    }


}
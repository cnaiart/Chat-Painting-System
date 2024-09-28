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

class ContentCensorEnum
{
    // 文本审核，图片审核
    const TYPE_TEXT = 1;
    const TYPE_IMAGE = 2;

    //审核状态
    const CENSOR_STATUS_WAIT = 0;//未审核
    const CENSOR_STATUS_COMPLIANCE = 1;//合规
    const CENSOR_STATUS_NON_COMPLIANCE = 2;//不合规
    const CENSOR_STATUS_SUSPECTED = 3;//疑似
    const CENSOR_STATUS_FAIL = 4;//审核失败

    /**
     * @notes 审核状态
     * @param $type
     * @return string
     * @author mjf
     * @date 2024/1/26 15:50
     */
    public static function getCensorStatusDesc($type): string
    {
        $desc =  [
            self::CENSOR_STATUS_WAIT => '未审核',
            self::CENSOR_STATUS_COMPLIANCE => '合规',
            self::CENSOR_STATUS_NON_COMPLIANCE => '不合规',
            self::CENSOR_STATUS_SUSPECTED => '疑似',
            self::CENSOR_STATUS_FAIL => '审核失败',
        ];
        return $desc[$type] ?? '';
    }

}
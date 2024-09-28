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

namespace app\adminapi\logic\qrcode;

use app\common\enum\qrcode\MewxEnum;
use app\common\enum\qrcode\QrcodeEnum;
use app\common\logic\BaseLogic;
use app\common\model\qrcode\QrcodeRecords;
use app\common\service\ConfigService;

/**
 * 艺术二维码
 * Class QrcodeRecordsLogic
 * @package app\adminapi\logic\qrcode
 */
class QrcodeRecordsLogic extends BaseLogic
{

    /**
     * @notes 删除
     * @param $ids
     * @author mjf
     * @date 2023/10/17 10:49
     */
    public static function delete($ids)
    {
        QrcodeRecords::destroy(function ($query) use ($ids) {
            $query->whereIn('id', $ids);
        });
    }

    /**
     * @notes 下拉选项
     * @return array
     * @author mjf
     * @date 2023/10/17 15:20
     */
    public static function option()
    {
        return [
            'model' => MewxEnum::getModel(),
            'template' => MewxEnum::getTemplate(),
            'draw_model' => QrcodeEnum::getAiModelName(),
        ];
    }

    /**
     * @notes 示例设置
     * @return array
     * @author mjf
     * @date 2023/10/17 15:42
     */
    public static function getExample()
    {
        return [
            'status' => ConfigService::get('art_qrcode_config', 'example_status', 0),
            'content' => ConfigService::get('art_qrcode_config', 'example_content', ''),
        ];
    }

    /**
     * @notes 设置示例
     * @param $params
     * @return bool|string
     * @author mjf
     * @date 2023/10/17 15:54
     */
    public static function setExample($params)
    {
        if (!isset($params['status']) || !in_array($params['status'], [0, 1])) {
            return '请选择示例提示状态';
        }

        ConfigService::set('art_qrcode_config', 'example_status', $params['status']);
        ConfigService::set('art_qrcode_config', 'example_content', $params['content'] ?? '');
        return true;
    }

}
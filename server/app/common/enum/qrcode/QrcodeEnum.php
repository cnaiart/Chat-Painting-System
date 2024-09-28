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

namespace app\common\enum\qrcode;

/**
 * 艺术二维码
 * Class QrcodeEnum
 * @package app\common\enum\qrcode
 */
class QrcodeEnum
{
    // 状态
    const STATUS_NOT = 0; // 待处理
    const STATUS_IN_PROGRESS = 1; // 执行中
    const STATUS_FAIL = 2; // 失败
    const STATUS_SUCCESS = 3; // 成功

    // 生成类型
    const TYPE_TEXT = 1; // 文本模式
    const TYPE_IMAGE = 2; // 图片模式

    // 绘画api配置
    const API_MEWX = 'mewx';
    const API_ZHISHUYUN = 'zhishuyun_qrcode';

    // 生成模式
    const WAY_SELF = 1; // 自定义
    const WAY_TEMPLATE = 2; // 模板

    /**
     * @notes 模型名称
     * @param bool $from
     * @return string|string[]
     * @author mjf
     * @date 2023/10/16 16:47
     */
    public static function getAiModelName($from = true)
    {
        $desc = [
            self::API_MEWX       => '星月熊',
            self::API_ZHISHUYUN  => '知数云',
        ];
        if(true === $from) {
            return $desc;
        }
        return $desc[$from] ?? '';
    }

    /**
     * @notes 默认配置
     * @param bool $from
     * @return array|array[]
     * @author mjf
     * @date 2023/10/16 16:47
     */
    public static function getDefaultCodeConfig($from = true)
    {
        $desc = [
            // 星月熊
            self::API_MEWX => [
                'name' => '星月熊',
                'status' => 1,
                // v1=1, v1.1=2, v2=3, v3=4
                'version' => ['1','2','3','4'],
                'is_open' => 1,
                'proxy_url' => '',
                'type' => self::API_MEWX,
            ],
            // 知数云
            self::API_ZHISHUYUN => [
                'name' => '知数云',
                'status' => 0,
                'version' => [],
                'is_open' => 1,
                'type' => self::API_ZHISHUYUN,
            ],
        ];
        if (true === $from) {
            return $desc;
        }
        return $desc[$from] ?? [];
    }

    /**
     * @notes 模型计费
     * @param bool $from
     * @return array|array[]
     * @author mjf
     * @date 2023/10/17 10:05
     */
    public static function getDefaultBillingConfig($from = true)
    {
        $desc = [
            // 星月熊
            self::API_MEWX => [
                'name'              => '星月熊',
                'key'               => self::API_MEWX,
                'status'            => 0,
                'alias'             => '',
                'balance'           => '',
                'member_free'       => 0,
            ],
            // 知数云
            self::API_ZHISHUYUN => [
                'name'              => '知数云',
                'key'               => self::API_ZHISHUYUN,
                'status'            => 0,
                'alias'             => '',
                'balance'           => '',
                'member_free'       => 0,
            ],
        ];
        if (true === $from) {
            return $desc;
        }
        return $desc[$from] ?? [];
    }

    /**
     * @notes 获取模板名称
     * @param $drawModel
     * @param $template
     * @return array[]|mixed|string
     * @author mjf
     * @date 2023/11/10 14:52
     */
    public static function getTemplateName($drawModel, $template)
    {
        if ($drawModel == self::API_MEWX) {
            return MewxEnum::getTemplate($template);
        }

        if ($drawModel == self::API_ZHISHUYUN) {
            return ZsyEnum::getTemplate($template);
        }

        return '';
    }


}
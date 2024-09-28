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
// | author=>likeshopTeam
// +----------------------------------------------------------------------
namespace app\common\enum\qrcode;

/**
 * 知数云
 * Class ZsyEnum
 * @package app\common\enum\qrcode
 */
class ZsyEnum
{
    const TEMPLATE = [
        [
            "value" => "sunset",
            "name" => "日落",
            "preview_img" => "resource/image/api/qrcode_template/10.jpeg",
        ],
        [
            "value" => "floral",
            "name" => "花卉",
            "preview_img" => "resource/image/api/qrcode_template/10.jpeg",
        ],
        [
            "value" => "snowflakes",
            "name" => "雪花",
            "preview_img" => "resource/image/api/qrcode_template/10.jpeg",
        ],
        [
            "value" => "feathers",
            "name" => "羽毛",
            "preview_img" => "resource/image/api/qrcode_template/10.jpeg",
        ],
        [
            "value" => "raindrops",
            "name" => "雨滴",
            "preview_img" => "resource/image/api/qrcode_template/10.jpeg",
        ],
        [
            "value" => "ultra-realism",
            "name" => "超现实",
            "preview_img" => "resource/image/api/qrcode_template/10.jpeg",
        ],
        [
            "value" => "epic-realms",
            "name" => "史诗领域",
            "preview_img" => "resource/image/api/qrcode_template/10.jpeg",
        ],
        [
            "value" => "intricate-studio",
            "name" => "错综复杂",
            "preview_img" => "resource/image/api/qrcode_template/10.jpeg",
        ],
        [
            "value" => "symmetric-masterpiece",
            "name" => "对称杰作",
            "preview_img" => "resource/image/api/qrcode_template/10.jpeg",
        ],
        [
            "value" => "luminous-highway",
            "name" => "发光高速公路",
            "preview_img" => "resource/image/api/qrcode_template/10.jpeg",
        ],
        [
            "value" => "celestial-journey",
            "name" => "星际之旅",
            "preview_img" => "resource/image/api/qrcode_template/10.jpeg",
        ],
        [
            "value" => "neon-mech",
            "name" => "霓虹机械",
            "preview_img" => "resource/image/api/qrcode_template/10.jpeg",
        ],
    ];

    // pixel
    const PIXEL_STYLE = [
        'square' => [
            'name' => '方形',
            'value' => 'square',
            'preview_img' => 'resource/image/api/qrcode_other/square.png',
        ],
        'rounded' => [
            'name' => '圆角',
            'value' => 'rounded',
            'preview_img' => 'resource/image/api/qrcode_other/rounded.png',
        ],
        'dot' => [
            'name' => '点状',
            'value' => 'dot',
            'preview_img' => 'resource/image/api/qrcode_other/dot.png',
        ],
    ];

    /**
     * @notes 获取模板
     * @param bool $from
     * @return array[]|mixed|string
     * @author mjf
     * @date 2023/10/17 14:26
     */
    public static function getTemplate($from = true)
    {
        if ($from === true) {
            $data = [];
            foreach (self::TEMPLATE as $item) {
                $item['preview_img'] = request()->domain() . '/' . $item['preview_img'];
                $data[] = $item;
            }
            return $data;
        }
        $list = array_column(self::TEMPLATE, null, 'value');
        return $list[$from]['name'] ?? '-';
    }

    /**
     * @notes 像素风格
     * @param bool $from
     * @return array|mixed|string
     * @author mjf
     * @date 2023/11/10 14:29
     */
    public static function getPixelStyle($from = true)
    {
        if ($from === true) {
            $data = [];
            foreach (self::PIXEL_STYLE as $item) {
                $item['preview_img'] = request()->domain() . '/' . $item['preview_img'];
                $data[] = $item;
            }
            return $data;
        }
        $list = array_column(self::PIXEL_STYLE, null, 'value');
        return $list[$from]['name'] ?? '-';
    }
}
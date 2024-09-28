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
 * 星月熊
 * Class MewxEnum
 * @package app\common\enum
 */
class MewxEnum
{
    const TEMPLATE = [
        [
            "id" => 10,
            "name" => "蛋糕",
            "preview_img" => "resource/image/api/qrcode_template/10.jpeg",
            "is_vip" => 2,
            "points" => 5
        ],
        [
            "id" => 12,
            "name" => "玫瑰礼服",
            "preview_img" => "resource/image/api/qrcode_template/12.jpeg",
            "is_vip" => 1,
            "points" => 5
        ],
        [
            "id" => 13,
            "name" => "公主裙",
            "preview_img" => "resource/image/api/qrcode_template/13.jpeg",
            "is_vip" => 1,
            "points" => 5
        ],
        [
            "id" => 2,
            "name" => "墨莲",
            "preview_img" => "resource/image/api/qrcode_template/2.jpeg",
            "is_vip" => 1,
            "points" => 5
        ],
        [
            "id" => 3,
            "name" => "钢铁侠",
            "preview_img" => "resource/image/api/qrcode_template/3.jpeg",
            "is_vip" => 0,
            "points" => 1
        ],
        [
            "id" => 53,
            "name" => "怦然心动",
            "preview_img" => "resource/image/api/qrcode_template/53.jpeg",
            "is_vip" => 1,
            "points" => 5,
            "iw" => 0.28
        ],
        [
            "id" => 54,
            "name" => "星河与你",
            "preview_img" => "resource/image/api/qrcode_template/54.jpeg",
            "is_vip" => 1,
            "points" => 10,
            "iw" => 0.28
        ],
        [
            "id" => 55,
            "name" => "月落星沉",
            "preview_img" => "resource/image/api/qrcode_template/55.jpeg",
            "is_vip" => 1,
            "points" => 5,
            "iw" => 0.28
        ],
        [
            "id" => 23,
            "name" => "迷雾城堡",
            "preview_img" => "resource/image/api/qrcode_template/23.jpeg",
            "is_vip" => 1,
            "points" => 5,
            "iw" => 0.3
        ],
        [
            "id" => 72,
            "name" => "珊瑚",
            "preview_img" => "resource/image/api/qrcode_template/72.jpeg",
            "is_vip" => 1,
            "points" => 5,
            "iw" => 0.3
        ],
        [
            "id" => 75,
            "name" => "青山",
            "preview_img" => "resource/image/api/qrcode_template/75.jpeg",
            "is_vip" => 1,
            "points" => 5,
            "iw" => 0.3
        ],
        [
            "id" => 82,
            "name" => "月下枫林",
            "preview_img" => "resource/image/api/qrcode_template/82.jpeg",
            "is_vip" => 2,
            "points" => 5,
            "iw" => 0.5
        ]
    ];

    const MODEL = [
        [
            "name" => "卡通插画",
            "id" => 67,
            "preview_img" => "resource/image/api/qrcode_model/cartoon.png",
        ],
        [
            "name" => "糖果头像",
            "id" => 51,
            "preview_img" => "resource/image/api/qrcode_model/candy_head.png",
        ],
        [
            "name" => "水墨画",
            "id" => 69,
            "preview_img" => "resource/image/api/qrcode_model/wash_painting.png",
        ],
        [
            "name" => "美漫",
            "id" => 70,
            "preview_img" => "resource/image/api/qrcode_model/anime4.png",
        ],
        [
            "name" => "彩漫4",
            "id" => 74,
            "preview_img" => "resource/image/api/qrcode_model/anime4.png",
        ]
    ];

    const VERSION = [
        1 => [
            'name' => 'v1',
            'value' => '1',
        ],
        2 => [
            'name' => 'v1.1',
            'value' => '1.1',
        ],
        3 => [
            'name' => 'v2',
            'value' => '2',
        ],
        4 => [
            'name' => 'v3',
            'value' => '3',
        ],
    ];

    /**
     * @notes 返回匹配版本号
     * @param $qrcodeVersion
     * @return array
     * @author mjf
     * @date 2023/10/18 18:33
     */
    public static function getVersion($qrcodeVersion)
    {
        if (empty($qrcodeVersion) || !is_array($qrcodeVersion)) {
            return [];
        }

        $result = [];
        foreach ($qrcodeVersion as $item) {
            if (isset(self::VERSION[$item])) {
                $version = self::VERSION[$item];
                $result[$version['name']] = $version['value'];
            }
        }
        return $result;
    }

    /**
     * @notes 获取模型
     * @param bool $from
     * @return string|\string[][]
     * @author mjf
     * @date 2023/10/17 14:23
     */
    public static function getModel($from = true)
    {
        if ($from === true) {
            $data = [];
            foreach (self::MODEL as $item) {
                $item['preview_img'] = request()->domain() . '/' . $item['preview_img'];
                $data[] = $item;
            }
            return $data;
        }
        $list = array_column(self::MODEL, null, 'id');
        return $list[$from]['name'] ?? '-';
    }

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
        $list = array_column(self::TEMPLATE, null, 'id');
        return $list[$from]['name'] ?? '-';
    }


}
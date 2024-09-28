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
namespace app\common\enum;

use app\common\enum\chat\ChatEnum;

/**
 * 绘图枚举
 * Class DrawEnum
 * @package app\common\enum
 */
class DrawEnum
{
    // 状态
    const STATUS_NOT = 0; // 待处理
    const STATUS_IN_PROGRESS = 1; // 执行中
    const STATUS_FAIL = 2; // 失败
    const STATUS_SUCCESS = 3; // 成功

    // 灵犀绘图回调状态
    const STATUS_NOT_TEXT = "NOT_START"; // 待处理
    const STATUS_IN_PROGRESS_TEXT = "IN_PROGRESS"; // 执行中
    const STATUS_FAIL_TEXT = "FAILURE"; // 失败
    const STATUS_SUCCESS_TEXT = "SUCCESS"; // 成功

    // 类型
    const TYPE_TEXT_TO_IMAGE = 1; // 文生图
    const TYPE_IMAGE_TO_IMAGE = 2; // 图生图
    const TYPE_UPSCALE_IMAGE = 3; // 图变大
    const TYPE_VARIATION_IMAGE = 4; // 图变化

    // 绘画api配置
    const API_ZHISHUYUN_FAST = 'zhishuyun_fast';
    const API_ZHISHUYUN_RELAX = 'zhishuyun_relax';
    const API_ZHISHUYUN_TURBO = 'zhishuyun_turbo';
    const API_MDDAI_MJ = 'mddai_mj';
    const API_SD = 'sd';
    const API_YIJIAN_SD = 'yijian_sd';
    const API_DALLE3 = 'dalle3';

    // 绘画操作
    const ACTION_GENERATE = "generate"; // 生图
    const ACTION_UPSAMPLE = "upsample"; // 放大
    const ACTION_VARIATION = "variation"; // 变换

    // mj绘画版本
    const MJ_VERSION_6 = "--v 6.0";
    const MJ_VERSION_52 = "--v 5.2";
    const MJ_VERSION_51 = "--v 5.1";
    const MJ_VERSION_5 = "--v 5";
    const MJ_VERSION_4 = "--v 4";
    const MJ_VERSION_NIJI5 = "--niji 5";

    /**
     * @notes 获取模型名称
     * @param bool $from
     * @return array|mixed
     * @author cjhao
     * @date 2023/7/25 14:49
     */
    public static function getAiModelName($from = true)
    {
        $desc = [
            self::API_ZHISHUYUN_FAST       => '知数云-快速MJ',
            self::API_ZHISHUYUN_RELAX      => '知数云-慢速MJ',
            self::API_ZHISHUYUN_TURBO      => '知数云-极速MJ',
            self::API_MDDAI_MJ             => '官方直连-MJ',
            self::API_YIJIAN_SD            => '意间SD',
            self::API_DALLE3               => 'DALLE-3',
            self::API_SD                   => 'SD绘图',
        ];
        if(true === $from) {
            return $desc;
        }
        return $desc[$from] ?? '';
    }

    /**
     * @notes
     * @param bool $from
     * @return array|string[]|\string[][]
     * @author 段誉
     * @date 2023/7/7 11:26
     */
    public static function getDrawDefaultConfig($from = true)
    {
        $desc = [
            // 知数云fast
            self::API_ZHISHUYUN_FAST => [
                'name' => '知数云-快速MJ',
                'status' => 1,
                'token' => "",
                'auto_translate' => 0,
                'translate_type'    => 1,   //1-系统翻译；2-手动翻译;
                'is_open' => 1,
                'proxy_url' => '',
            ],
            // 知数云relax
            self::API_ZHISHUYUN_RELAX => [
                'name' => '知数云-慢速MJ',
                'status' => 0,
                'token' => "",
                'auto_translate' => 0,
                'translate_type'    => 1,   //1-系统翻译；2-手动翻译;
                'is_open' => 1,
                'proxy_url' => '',
            ],
            // 知数云turbo
            self::API_ZHISHUYUN_TURBO => [
                'name' => '知数云-极速MJ',
                'status' => 0,
                'token' => "",
                'auto_translate' => 0,
                'translate_type'    => 1,
                'is_open' => 1,
                'proxy_url' => '',
            ],
            // 官方直连-MJ
            self::API_MDDAI_MJ => [
                'name' => '官方直连-MJ',
                'status' => 0,
                'token' => "",
                'auto_translate' => 0,
                'translate_type'    => 1,   //1-系统翻译；2-手动翻译;
                'is_open' => 1,
                'proxy_url' => '', // 图片代理地址
                'proxy_api' => '', // api代理地址
                'proxy_wss' => '', // wss代理地址
                'guild_id' => '', // discord-guild_id
                'channel_id' => '', // discord-channel_id
                'session_id' => '', // discord-session_id
                'bot_token' => '', // discord-bot_token
            ],
            // 意间绘图
            self::API_YIJIAN_SD => [
                'name' => '意间SD',
                'status' => 0,
                'token' => "",
                'auto_translate' => 0,
                'translate_type'    => 1,   //1-系统翻译；2-手动翻译;
                'is_open' => 1,
                'proxy_url' => '',
            ],
            // dalle3
            self::API_DALLE3 => [
                'name' => 'DALLE-3',
                'status' => 0,
                'token' => "",
                'auto_translate' => 0,
                'translate_type'    => 1,   //1-系统翻译；2-手动翻译;
                'is_open' => 1,
                'proxy_url' => '',
            ],
            // sd绘图
            self::API_SD => [
                'name' => 'SD绘图',
                'status' => 0,
                'token' => "",
                'auto_translate' => 0,
                'translate_type'    => 1,   //1-系统翻译；2-手动翻译;
                'is_open' => 1,
                'proxy_url' => '',
            ],
        ];
        if (true === $from) {
            return $desc;
        }
        return $desc[$from] ?? [];
    }

    /**
     * @notes 计费模型默认配置
     * @param bool $from
     * @return array|mixed
     * @author cjhao
     * @date 2023/7/18 19:00
     */
    public static function getDefaultBillingConfig($from = true)
    {
        $desc = [
            // 知数云fast
            self::API_ZHISHUYUN_FAST => [
                'name'              => '知数云-快速MJ',
                'key'               => self::API_ZHISHUYUN_FAST,
                'status'            => 0,
                'alias'             => '',
                'balance'           => '',
                'member_free'       => 0,
            ],
            // 知数云relax
            self::API_ZHISHUYUN_RELAX => [
                'name'              => '知数云-慢速MJ',
                'key'               => self::API_ZHISHUYUN_RELAX,
                'status'            => 0,
                'alias'             => '',
                'balance'           => '',
                'member_free'       => 0,
            ],
            // 知数云turbo
            self::API_ZHISHUYUN_TURBO => [
                'name'              => '知数云-极速MJ',
                'key'               => self::API_ZHISHUYUN_TURBO,
                'status'            => 0,
                'alias'             => '',
                'balance'           => '',
                'member_free'       => 0,
            ],
            // 官方直连-MJ
            self::API_MDDAI_MJ => [
                'name'              => '官方直连-MJ',
                'key'               => self::API_MDDAI_MJ,
                'status'            => 0,
                'alias'             => '',
                'balance'           => '',
                'member_free'       => 0,
            ],
            // 意间-SD
            self::API_YIJIAN_SD => [
                'name'              => '意间-SD',
                'key'               => self::API_YIJIAN_SD,
                'status'            => 0,
                'alias'             => '',
                'balance'           => '',
                'member_free'       => 0,
            ],
            // DALLE 3
            self::API_DALLE3 => [
                'name'              => 'DALLE-3',
                'key'               => self::API_DALLE3,
                'status'            => 0,
                'alias'             => '',
                'balance'           => '',
                'member_free'       => 0,
            ],
            // 本地-SD
            self::API_SD => [
                'name'              => 'SD绘图',
                'key'               => self::API_SD,
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
     * @notes 获取翻译配置
     * @return array
     * @author cjhao
     * @date 2023/7/17 17:15
     */
    public static function getTranslateConfig()
    {
        return [
            'model'     => ChatEnum::OPEN_GPT_35,
            'prompt'    => '我会用任何语言和你交流，你只需将我的话翻译为英语，不要解释我的话或者回复其他信息，请立刻将我的话翻译返回，我的话是:{prompt}',
        ];
    }

    /**
     * @notes 获取绘画操作及图片索引
     * @param string $action
     * @return array|int[]
     * @author 段誉
     * @date 2023/8/3 15:36
     */
    public static function getActionAndIndex(string $action)
    {
        if (str_contains($action, DrawEnum::ACTION_UPSAMPLE)) {
            return [
                'action' => DrawEnum::ACTION_UPSAMPLE,
                'index' => (int)mb_substr($action, -1),
            ];
        }

        if (str_contains($action, DrawEnum::ACTION_VARIATION)) {
            return [
                'action' => DrawEnum::ACTION_VARIATION,
                'index' => (int)mb_substr($action, -1),
            ];
        }

        return [
            'action' => DrawEnum::ACTION_GENERATE,
            'index' => 0,
        ];
    }

    /**
     * @notes mj绘画版本
     * @return \string[][]
     * @author mjf
     * @date 2023/9/20 10:23
     */
    public static function getMjVersion()
    {
        $mj = [
            self::MJ_VERSION_6 => '6.0',
            self::MJ_VERSION_52 => '5.2',
            self::MJ_VERSION_51 => '5.1',
            self::MJ_VERSION_5 => '5',
            self::MJ_VERSION_4 => '4',
        ];

        $niji = [
            self::MJ_VERSION_NIJI5 => '5',
        ];

        return [
            'mj' => $mj,
            'niji' => $niji,
        ];
    }

    /**
     * @notes MJ绘画niji风格
     * @return string[]
     * @author mjf
     * @date 2023/9/25 16:39
     */
    public static function getMjStyle()
    {
        return [
            'default' => "动漫",
            'cute' => "可爱",
            'expressive' => "丰富",
            'scenic' => "风景",
        ];
    }
}
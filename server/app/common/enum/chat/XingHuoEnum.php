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
namespace app\common\enum\chat;
/**
 * 星火认知大模型
 * Class ZhiPuAiEnum
 * @package app\common\enum
 */
class XingHuoEnum
{

    const XINGHUO15              = "xinghuo1.5";
    const XINGHUO20              = "xinghuo2.0";
    const XINGHUO30              = "xinghuo3.0";

    const XINGHUO35              = "xinghuo3.5";

    const DAFAULT_CONFIG = [
        'xinghuo_model'         => self::XINGHUO15,
        'context_num'           => 3,               //上下文总数
        'temperature'           => 1.0,             //词汇属性 取值区间为[0.0，2.0]
        'top_p'                 => 1.0,               //随机属性 取值区间为[0.0, 1.0]
    ];


    /**
     * @notes 获取模型名称
     * @return array|string
     * @author cjhao
     * @date 2023/4/21 14:55
     */
    public static function getAliasNameList($from = true){
        $desc = [
            self::XINGHUO15 => '星火大模型V1.5',
            self::XINGHUO20 => '星火大模型V2.0',
            self::XINGHUO30 => '星火大模型V3.0',
            self::XINGHUO35 => '星火大模型V3.5',
        ];
        if(true === $from){
            return $desc;
        }
        return $desc[$from] ?? '';
    }

}
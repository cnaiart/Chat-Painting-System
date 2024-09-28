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
 * 百度ai枚举类
 * Class BaiduAiEnum
 * @package app\common\enum
 */
class WenXinEnum
{

    const ERNIE_BOT           = 'ERNIE-Bot';
    const ERNIE_BOT_TURBO     = 'ERNIE-Bot-turbo';
    const ERNIE_BOT4          = 'ERNIE-Bot-4';

    //百度ai默认配置
    const DAFAULT_CONFIG = [
        'wenxin_model'      => self::ERNIE_BOT,
        'temperature'       => 0.95,        //较高的数值会使输出更加随机，而较低的数值会使其更加集中和确定，取值范围(0, 1.0]
        'top_p'             => 0.8,         //影响输出文本的多样性，取值越大，生成文本的多样性越强，取值范围[0, 1.0]
        'context_num'       => 3,           //上下文总数
    ];
}
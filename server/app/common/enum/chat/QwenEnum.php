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
 * 通义千问枚举类
 */
class QwenEnum
{
    const QWEN_TURBO                  = 'qwen-turbo';
    const QWEN_PLUS                   = 'qwen-plus';
    const QWEN_MAX                    = 'qwen-max';
    const QWEN_MAX_1201               = 'qwen-max-1201';
    const QWEN_MAX_LONGCONTEXT        = 'qwen-max-longcontext';

    //默认配置
    const DAFAULT_CONFIG = [
        'qwen_model'            => self::QWEN_TURBO,
        'top_p'                 => 0.8,//取值范围为（0,1.0)，取值越大，生成的随机性越高；取值越低，生成的随机性越低。
        'top_k'                 => 50,//取值越大，生成的随机性越高；取值越小，生成的确定性越高。
        'repetition_penalty'    => 1.1,//控制模型生成时的重复度, 1.0表示不做惩罚
        'temperature'           => 1.0,//用于控制随机性和多样性的程度 取值范围： [0, 2)
        'context_num'           => 3,//上下文总数
    ];

}
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
 * Azure OpenAI 枚举类
 */
class AzureOpenAIEnum
{
    const AZURE_GPT35_TURBO            = "azure_gpt35-turbo";
    const AZURE_GPT35_TURBO_16K        = "azure_gpt35-turbo-16k";

    const AZURE_GPT4                   = "azure_gpt4";
    const AZURE_GPT4_32k               = "azure_gpt4-32k";
    const AZURE_GPT4_VISION_PREVIEW    = "azure_gpt4-vision-preview";


    const DAFAULT_CONFIG = [                            //AI聊天参数默认值
        'azure_gpt35_model'    => self::AZURE_GPT35_TURBO,
        'azure_gpt4_model'    => self::AZURE_GPT4,
        'context_num'           => 3,           //上下文总数
        'temperature'           => 0.7,         //词汇属性
        'top_p'                 => 0.9,         //随机属性
        'presence_penalty'      => 0.5,         //话题属性
        'frequency_penalty'     => 0.5,         //重复属性
        'n'                     => 1,           //最大回复
    ];

}
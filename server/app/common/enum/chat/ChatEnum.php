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
 * 对话模型枚举类
 * Class ChatEnum
 * @package app\common\enum
 */
class ChatEnum
{

    const ZHIPUGLM          = 'chatglm';           //智谱GLM

    const OPEN_GPT_35       = 'gpt3.5';            //GPT-3.5
    const OPEN_GPT_40       = 'gpt4.0';            //GPT-4.0

    const API2D_35          = 'api2d3.5';          //api2d3.5
    const API2D_40          = 'api2d4.0';           //api2d40
    const XINGHUO           = 'xinghuo';           //讯飞星火
    const WENXIN            = 'wenxin';            //文心一言
    const QWEN              = 'qwen';               //通义千问
    const HUNYUAN           = 'hunyuan';            //腾讯混元
    const AZURE_OPEN_GPT_35 = 'azure_gpt3.5';       //Azure GPT-3.5
    const AZURE_OPEN_GPT_40 = 'azure_gpt4.0';       //Azure GPT-4.0

    const MINIMAX           = 'minimax';            //minimax

    const GEMINI            = 'gemini';             //geminis


    const OPENAIMODEL  = [
        self::OPEN_GPT_35,
        self::OPEN_GPT_40,
        self::API2D_35,
        self::API2D_40,

    ];

    /**
     * @notes 获取名称
     * @param bool $from
     * @return array|mixed
     * @author cjhao
     * @date 2023/6/13 14:49
     */
    public static function getChatName($from = true)
    {
        $desc = [
            self::ZHIPUGLM          => '智谱Ai',
            self::OPEN_GPT_35       => 'gpt3.5',
            self::OPEN_GPT_40       => 'gpt4.0',
            self::API2D_35          => 'api2d3.5',
            self::API2D_40          => 'api2d4.0',
            self::XINGHUO           => '讯飞星火',
            self::WENXIN            => '文心一言',
            self::QWEN              => '通义千问',
            self::HUNYUAN           => '腾讯混元',
            self::AZURE_OPEN_GPT_35 => 'Azure GPT-3.5',
            self::AZURE_OPEN_GPT_40 => 'Azure GPT-4.0',
            self::MINIMAX           => 'MiniMax',
            self::GEMINI            => 'Gemini',
        ];
        if(true === $from) {
            return $desc;
        }
        return $desc[$from] ?? '';
    }

    /**
     * 获取模型名称
     * @param $from
     * @param $chatKey
     * @return array|mixed|string
     */
    public static function getChatModelLists($from = true,$chatKey = true){
        $chatLists = [
            self::ZHIPUGLM      => [
//                ZhiPuEnum::CHATGLM_STD                      => 'chatGLM-Std',
//                ZhiPuEnum::CHATGLM_LITE                     => 'chatGLM-Lite',
//                ZhiPuEnum::CHATGLM_PRO                      => 'chatGLM-Pro',
                ZhiPuEnum::CHATGLM_TURBO                    => ZhiPuEnum::getAliasNameList(ZhiPuEnum::CHATGLM_TURBO),
                ZhiPuEnum::CHATGLM_4                        => ZhiPuEnum::getAliasNameList(ZhiPuEnum::CHATGLM_4),
            ],
            self::OPEN_GPT_35   => [
                OpenAiEnum::GPT35_TURBO                     => OpenAiEnum::GPT35_TURBO,
                OpenAiEnum::GPT35_TURBO_0301                => OpenAiEnum::GPT35_TURBO_0301,
                OpenAiEnum::GPT35_TURBO_0613                => OpenAiEnum::GPT35_TURBO_0613,
                OpenAiEnum::GPT35_TURBO_1106                => OpenAiEnum::GPT35_TURBO_1106,
                OpenAiEnum::GPT35_TURBO_16K                 => OpenAiEnum::GPT35_TURBO_16K,
                OpenAiEnum::GPT35_TURBO_16K_0613            => OpenAiEnum::GPT35_TURBO_16K_0613,
            ],
            self::OPEN_GPT_40   => [
                OpenAiEnum::GPT4                            => OpenAiEnum::GPT4,
                OpenAiEnum::GPT4_0314                       => OpenAiEnum::GPT4_0314,
                OpenAiEnum::GPT4_0613                       => OpenAiEnum::GPT4_0613,
                OpenAiEnum::GPT4_1106_PREVIEW               => OpenAiEnum::GPT4_1106_PREVIEW,
                OpenAiEnum::GPT4_32k                        => OpenAiEnum::GPT4_32k,
                OpenAiEnum::GPT4_32k_0314                   => OpenAiEnum::GPT4_32k_0314,
            ],
            self::API2D_35      => [
                Api2dEnum::GPT35_TURBO                      => Api2dEnum::GPT35_TURBO,
                Api2dEnum::GPT35_TURBO_0301                 => Api2dEnum::GPT35_TURBO_0301,
                Api2dEnum::GPT35_TURBO_0613                 => Api2dEnum::GPT35_TURBO_0613,
                Api2dEnum::GPT35_TURBO_1106                 => Api2dEnum::GPT35_TURBO_1106,
                Api2dEnum::GPT35_TURBO_16K                  => Api2dEnum::GPT35_TURBO_16K,
                Api2dEnum::GPT35_TURBO_16K_0613             => Api2dEnum::GPT35_TURBO_16K_0613,
            ],
            self::API2D_40      => [
                Api2dEnum::GPT4                             => Api2dEnum::GPT4,
                Api2dEnum::GPT4_0314                        => Api2dEnum::GPT4_0314,
                Api2dEnum::GPT4_0613                        => Api2dEnum::GPT4_0613,
                Api2dEnum::GPT4_1106_PREVIEW                => Api2dEnum::GPT4_1106_PREVIEW,
                Api2dEnum::GPT4_32k                         => Api2dEnum::GPT4_32k,
                Api2dEnum::GPT4_32k_0314                    => Api2dEnum::GPT4_32k_0314,
            ],
            self::XINGHUO       => [
                XingHuoEnum::XINGHUO15                      => XingHuoEnum::getAliasNameList(XingHuoEnum::XINGHUO15),
                XingHuoEnum::XINGHUO20                      => XingHuoEnum::getAliasNameList(XingHuoEnum::XINGHUO20),
                XingHuoEnum::XINGHUO30                      => XingHuoEnum::getAliasNameList(XingHuoEnum::XINGHUO30),
                XingHuoEnum::XINGHUO35                      => XingHuoEnum::getAliasNameList(XingHuoEnum::XINGHUO35),
            ],
            self::WENXIN        => [
                WenXinEnum::ERNIE_BOT                       => WenXinEnum::ERNIE_BOT,
                WenXinEnum::ERNIE_BOT_TURBO                 => WenXinEnum::ERNIE_BOT_TURBO,
                WenXinEnum::ERNIE_BOT4                      => WenXinEnum::ERNIE_BOT4,
            ],
            self::QWEN          => [
                QwenEnum::QWEN_TURBO                        => QwenEnum::QWEN_TURBO,
                QwenEnum::QWEN_PLUS                         => QwenEnum::QWEN_PLUS,
                QwenEnum::QWEN_MAX                          => QwenEnum::QWEN_MAX,
                QwenEnum::QWEN_MAX_1201                     => QwenEnum::QWEN_MAX_1201,
                QwenEnum::QWEN_MAX_LONGCONTEXT              => QwenEnum::QWEN_MAX_LONGCONTEXT,
            ],
            self::HUNYUAN          => [
                HunYuanEnum::HUNYUAN_STD                    => HunYuanEnum::HUNYUAN_STD,
                HunYuanEnum::HUNYUAN_PRO                    => HunYuanEnum::HUNYUAN_PRO,
            ],
            self::AZURE_OPEN_GPT_35          => [
                AzureOpenAIEnum::AZURE_GPT35_TURBO          => AzureOpenAIEnum::AZURE_GPT35_TURBO,
                AzureOpenAIEnum::AZURE_GPT35_TURBO_16K      => AzureOpenAIEnum::AZURE_GPT35_TURBO_16K,
            ],
            self::AZURE_OPEN_GPT_40          => [
                AzureOpenAIEnum::AZURE_GPT4                 => AzureOpenAIEnum::AZURE_GPT4,
                AzureOpenAIEnum::AZURE_GPT4_32k             => AzureOpenAIEnum::AZURE_GPT4_32k,
                AzureOpenAIEnum::AZURE_GPT4_VISION_PREVIEW  => AzureOpenAIEnum::AZURE_GPT4_VISION_PREVIEW,
            ],
            self::MINIMAX           => [
                MiniMaxEnum::ABAB55                         => MiniMaxEnum::ABAB55,
            ],
            self::GEMINI            => [
                GeminiEnum::GEMINI_PRO                      => GeminiEnum::GEMINI_PRO,
            ],

        ];
        if(true === $chatKey){
            $chatLists = array_values($chatLists);
            $lists = [];
            foreach ($chatLists as $chat){
                $lists = array_merge($lists,$chat);
            }
            if(true === $from){
                return $lists;
            }
            return $lists[$from] ?? $from;
        }
        return $chatLists[$chatKey] ?? [];
    }


    /**
     * @notes 获取模型默认配置
     * @param $from
     * @return array|array[]
     * @author cjhao
     * @date 2024/1/24 10:31
     */
    public static function getDefaultChatConfig($from = true)
    {
        $desc = [
            self::ZHIPUGLM      => [
                'status'            => 1,//默认开启6b
                'key'               => self::ZHIPUGLM,
                'name'              => self::getChatName(self::ZHIPUGLM),
                'model'             => ZhiPuEnum::DAFAULT_CONFIG['chatglm_turbo'],
                'context_num'       => ZhiPuEnum::DAFAULT_CONFIG['context_num'],
                'temperature'       => ZhiPuEnum::DAFAULT_CONFIG['temperature'],
                'top_p'             => ZhiPuEnum::DAFAULT_CONFIG['top_p'],
                'model_list'        => self::getChatModelLists(true,self::ZHIPUGLM),
                'source'            => 'zhipu',
                'is_open'           => 1,
            ],
            self::OPEN_GPT_35   => [
                'status'            => 0,
                'name'              => self::getChatName(self::OPEN_GPT_35),
                'key'               => self::OPEN_GPT_35,
                'api_key'           => [],
                'model'             => OpenAiEnum::DAFAULT_CONFIG['gpt3.5_model'],
                'model_list'        => self::getChatModelLists(true,self::OPEN_GPT_35),
                'context_num'       => OpenAiEnum::DAFAULT_CONFIG['context_num'],
                'temperature'       => OpenAiEnum::DAFAULT_CONFIG['temperature'],
                'top_p'             => OpenAiEnum::DAFAULT_CONFIG['top_p'],
                'presence_penalty'  => OpenAiEnum::DAFAULT_CONFIG['presence_penalty'],
                'frequency_penalty' => OpenAiEnum::DAFAULT_CONFIG['frequency_penalty'],
                'n'                 => OpenAiEnum::DAFAULT_CONFIG['n'],
                'source'            => 'openai',
                'is_open'           => 1,
            ],
            self::OPEN_GPT_40   => [
                'status'            => 0,
                'name'              => self::getChatName(self::OPEN_GPT_40),
                'key'               => self::OPEN_GPT_40,
                'api_key'           => [],
                'model'             => OpenAiEnum::DAFAULT_CONFIG['gpt4.0_model'],
                'model_list'        => self::getChatModelLists(true,self::OPEN_GPT_40),
                'context_num'       => OpenAiEnum::DAFAULT_CONFIG['context_num'],
                'temperature'       => OpenAiEnum::DAFAULT_CONFIG['temperature'],
                'top_p'             => OpenAiEnum::DAFAULT_CONFIG['top_p'],
                'presence_penalty'  => OpenAiEnum::DAFAULT_CONFIG['presence_penalty'],
                'frequency_penalty' => OpenAiEnum::DAFAULT_CONFIG['frequency_penalty'],
                'n'                 => OpenAiEnum::DAFAULT_CONFIG['n'],
                'source'            => 'openai',
                'is_open'           => 1,
            ],
            self::API2D_35      => [
                'status'            => 0,
                'name'              => self::getChatName(self::API2D_35),
                'key'               => self::API2D_35,
                'api_key'           => [],
                'model'             => Api2dEnum::DAFAULT_CONFIG['gpt3.5_model'],
                'model_list'        => self::getChatModelLists(true,self::API2D_35),
                'context_num'       => Api2dEnum::DAFAULT_CONFIG['context_num'],
                'temperature'       => Api2dEnum::DAFAULT_CONFIG['temperature'],
                'top_p'             => Api2dEnum::DAFAULT_CONFIG['top_p'],
                'presence_penalty'  => Api2dEnum::DAFAULT_CONFIG['presence_penalty'],
                'frequency_penalty' => Api2dEnum::DAFAULT_CONFIG['frequency_penalty'],
                'n'                 => Api2dEnum::DAFAULT_CONFIG['n'],
                'source'            => 'openai',
                'is_open'           => 1,

            ],
            self::API2D_40      => [
                'status'            => 0,
                'name'              => self::getChatName(self::API2D_40),
                'key'               => self::API2D_40,
                'api_key'           => [],
                'model'             => Api2dEnum::DAFAULT_CONFIG['gpt4.0_model'],
                'model_list'        => self::getChatModelLists(true,self::API2D_40),
                'context_num'       => Api2dEnum::DAFAULT_CONFIG['context_num'],
                'temperature'       => Api2dEnum::DAFAULT_CONFIG['temperature'],
                'top_p'             => Api2dEnum::DAFAULT_CONFIG['top_p'],
                'presence_penalty'  => Api2dEnum::DAFAULT_CONFIG['presence_penalty'],
                'frequency_penalty' => Api2dEnum::DAFAULT_CONFIG['frequency_penalty'],
                'n'                 => Api2dEnum::DAFAULT_CONFIG['n'],
                'source'            => 'openai',
                'is_open'           => 1,

            ],
            self::XINGHUO        => [
                'key'               => self::XINGHUO,
                'name'              => self::getChatName(self::XINGHUO),
                'model'             => XingHuoEnum::DAFAULT_CONFIG['xinghuo_model'],
                'model_list'        => self::getChatModelLists(true,self::XINGHUO),
                'status'            => 0,
                'context_num'       => XingHuoEnum::DAFAULT_CONFIG['context_num'],
                'temperature'       => XingHuoEnum::DAFAULT_CONFIG['temperature'],
                'top_p'             => XingHuoEnum::DAFAULT_CONFIG['top_p'],
                'source'            => 'xunfei',
                'is_open'           => 1,
            ],
            self::WENXIN         => [
                'key'               => self::WENXIN,
                'name'              => self::getChatName(self::WENXIN),
                'model'             => WenXinEnum::DAFAULT_CONFIG['wenxin_model'],
                'model_list'        => self::getChatModelLists(true,self::WENXIN),
                'status'            => 0,
                'api_url'           => '',
                'temperature'       => WenXinEnum::DAFAULT_CONFIG['temperature'],
                'context_num'       => WenXinEnum::DAFAULT_CONFIG['context_num'],
                'top_p'             => WenXinEnum::DAFAULT_CONFIG['top_p'],
                'source'            => 'baidu',
                'is_open'           => 1,
            ],
            self::QWEN         => [
                'key'               => self::QWEN,
                'name'              => self::getChatName(self::QWEN),
                'model'             => QwenEnum::DAFAULT_CONFIG['qwen_model'],
                'model_list'        => self::getChatModelLists(true,self::QWEN),
                'status'            => 0,
                'top_p'             => QwenEnum::DAFAULT_CONFIG['top_p'],
                'top_k'             => QwenEnum::DAFAULT_CONFIG['top_k'],
                'repetition_penalty'=> QwenEnum::DAFAULT_CONFIG['repetition_penalty'],
                'temperature'       => QwenEnum::DAFAULT_CONFIG['temperature'],
                'context_num'       => QwenEnum::DAFAULT_CONFIG['context_num'],
                'source'            => 'qwen',
                'is_open'           => 1,
            ],
            self::HUNYUAN         => [
                'key'               => self::HUNYUAN,
                'name'              => self::getChatModelLists(true,self::HUNYUAN),
                'model'             => HunYuanEnum::DAFAULT_CONFIG['hunyuan_model'],
                'model_list'        => HunYuanEnum::getModelLists(),
                'status'            => 0,
                'top_p'             => HunYuanEnum::DAFAULT_CONFIG['top_p'],
                'temperature'       => HunYuanEnum::DAFAULT_CONFIG['temperature'],
                'context_num'       => HunYuanEnum::DAFAULT_CONFIG['context_num'],
                'source'            => 'hunyuan',
                'is_open'           => 1,
            ],
            self::AZURE_OPEN_GPT_35   => [
                'status'            => 0,
                'name'              => self::getChatName(self::AZURE_OPEN_GPT_35),
                'key'               => self::AZURE_OPEN_GPT_35,
                'api_key'           => [],
                'model'             => AzureOpenAIEnum::DAFAULT_CONFIG['azure_gpt35_model'],
                'model_list'        => self::getChatModelLists(true,self::AZURE_OPEN_GPT_35),
                'context_num'       => AzureOpenAIEnum::DAFAULT_CONFIG['context_num'],
                'temperature'       => AzureOpenAIEnum::DAFAULT_CONFIG['temperature'],
                'top_p'             => AzureOpenAIEnum::DAFAULT_CONFIG['top_p'],
                'presence_penalty'  => AzureOpenAIEnum::DAFAULT_CONFIG['presence_penalty'],
                'frequency_penalty' => AzureOpenAIEnum::DAFAULT_CONFIG['frequency_penalty'],
                'n'                 => AzureOpenAIEnum::DAFAULT_CONFIG['n'],
                'source'            => 'azure',
                'is_open'           => 1,
            ],
            self::AZURE_OPEN_GPT_40   => [
                'status'            => 0,
                'name'              => self::getChatName(self::AZURE_OPEN_GPT_40),
                'key'               => self::AZURE_OPEN_GPT_40,
                'api_key'           => [],
                'model'             => AzureOpenAIEnum::DAFAULT_CONFIG['azure_gpt4_model'],
                'model_list'        => self::getChatModelLists(true,self::AZURE_OPEN_GPT_40),
                'context_num'       => AzureOpenAIEnum::DAFAULT_CONFIG['context_num'],
                'temperature'       => AzureOpenAIEnum::DAFAULT_CONFIG['temperature'],
                'top_p'             => AzureOpenAIEnum::DAFAULT_CONFIG['top_p'],
                'presence_penalty'  => AzureOpenAIEnum::DAFAULT_CONFIG['presence_penalty'],
                'frequency_penalty' => AzureOpenAIEnum::DAFAULT_CONFIG['frequency_penalty'],
                'n'                 => AzureOpenAIEnum::DAFAULT_CONFIG['n'],
                'source'            => 'azure',
                'is_open'           => 1,
            ],
            //minimax
            self::MINIMAX           => [
                'status'            => 0,
                'name'              => self::getChatName(self::MINIMAX),
                'key'               => self::MINIMAX,
                'api_key'           => [],
                'model'             => MiniMaxEnum::DAFAULT_CONFIG['minimax_model'],
                'model_list'        => self::getChatModelLists(true,self::MINIMAX),
                'context_num'       => MiniMaxEnum::DAFAULT_CONFIG['context_num'],
                'temperature'       => MiniMaxEnum::DAFAULT_CONFIG['temperature'],
                'top_p'             => MiniMaxEnum::DAFAULT_CONFIG['top_p'],
                'source'            => 'minimax',
                'is_open'           => 1,
            ],
            //gemini
            self::GEMINI           => [
                'status'            => 0,
                'name'              => self::getChatName(self::GEMINI),
                'key'               => self::GEMINI,
                'api_key'           => [],
                'model'             => GeminiEnum::DAFAULT_CONFIG['gemini_model'],
                'model_list'        => self::getChatModelLists(true,self::GEMINI),
                'context_num'       => GeminiEnum::DAFAULT_CONFIG['context_num'],
                'temperature'       => GeminiEnum::DAFAULT_CONFIG['temperature'],
                'top_p'             => GeminiEnum::DAFAULT_CONFIG['top_p'],
                'source'            => 'google',
                'is_open'           => 1,
            ],
        ];
        if(true === $from){
            return $desc;
        }
        return  $desc[$from] ?? [];

    }

    /**
     * @notes 默认计费配置
     * @param bool $from
     * @return array|mixed
     * @author cjhao
     * @date 2023/7/18 17:14
     */
    public static function getDefaultBillingConfig($from = true)
    {
        $desc = [
            //智谱ai-turbo
            ZhiPuEnum::CHATGLM_TURBO        => [
                'chat_key'             => self::ZHIPUGLM,
                'key'               => ZhiPuEnum::CHATGLM_TURBO,
                'name'              => ZhiPuEnum::getAliasNameList(ZhiPuEnum::CHATGLM_TURBO),
                'status'            => 0,
                'alias'             => '',
                'balance'           => '',
                'member_free'       => 0,
            ],
            //智谱ai-4
            ZhiPuEnum::CHATGLM_4        => [
                'chat_key'             => self::ZHIPUGLM,
                'key'               => ZhiPuEnum::CHATGLM_4,
                'name'              => ZhiPuEnum::getAliasNameList(ZhiPuEnum::CHATGLM_4),
                'status'            => 0,
                'alias'             => '',
                'balance'           => '',
                'member_free'       => 0,
            ],
            //openai3.5-turbo
            OpenAiEnum::GPT35_TURBO         => [
                'chat_key'             => self::OPEN_GPT_35,
                'key'               => OpenAiEnum::GPT35_TURBO,
                'name'              => OpenAiEnum::GPT35_TURBO,
                'status'            => 0,
                'alias'             => '',
                'balance'           => '',
                'member_free'       => 0,
            ],
            //openai3.5-turbo
            OpenAiEnum::GPT35_TURBO_0301    => [
                'chat_key'             => self::OPEN_GPT_35,
                'key'               => OpenAiEnum::GPT35_TURBO_0301,
                'name'              => OpenAiEnum::GPT35_TURBO_0301,
                'status'            => 0,
                'alias'             => '',
                'balance'           => '',
                'member_free'       => 0,
            ],
            //openai3.5-turbo
            OpenAiEnum::GPT35_TURBO_0613    => [
                'chat_key'             => self::OPEN_GPT_35,
                'key'               => OpenAiEnum::GPT35_TURBO_0613,
                'name'              => OpenAiEnum::GPT35_TURBO_0613,
                'status'            => 0,
                'alias'             => '',
                'balance'           => '',
                'member_free'       => 0,
            ],
            //openai3.5-turbo
            OpenAiEnum::GPT35_TURBO_1106    => [
                'chat_key'             => self::OPEN_GPT_35,
                'key'               => OpenAiEnum::GPT35_TURBO_1106,
                'name'              => OpenAiEnum::GPT35_TURBO_1106,
                'status'            => 0,
                'alias'             => '',
                'balance'           => '',
                'member_free'       => 0,
            ],
            //openai3.5-turbo
            OpenAiEnum::GPT35_TURBO_16K    => [
                'chat_key'             => self::OPEN_GPT_35,
                'key'               => OpenAiEnum::GPT35_TURBO_16K,
                'name'              => OpenAiEnum::GPT35_TURBO_16K,
                'status'            => 0,
                'alias'             => '',
                'balance'           => '',
                'member_free'       => 0,
            ],
            OpenAiEnum::GPT35_TURBO_16K_0613    => [
                'chat_key'             => self::OPEN_GPT_35,
                'key'               => OpenAiEnum::GPT35_TURBO_16K_0613,
                'name'              => OpenAiEnum::GPT35_TURBO_16K_0613,
                'status'            => 0,
                'alias'             => '',
                'balance'           => '',
                'member_free'       => 0,
            ],
            OpenAiEnum::GPT4    => [
                'chat_key'             => self::OPEN_GPT_40,
                'key'               => OpenAiEnum::GPT4,
                'name'              => OpenAiEnum::GPT4,
                'status'            => 0,
                'alias'             => '',
                'balance'           => '',
                'member_free'       => 0,
            ],
            OpenAiEnum::GPT4_0314    => [
                'chat_key'             => self::OPEN_GPT_40,
                'key'               => OpenAiEnum::GPT4_0314,
                'name'              => OpenAiEnum::GPT4_0314,
                'status'            => 0,
                'alias'             => '',
                'balance'           => '',
                'member_free'       => 0,
            ],
            OpenAiEnum::GPT4_0613    => [
                'chat_key'             => self::OPEN_GPT_40,
                'key'               => OpenAiEnum::GPT4_0613,
                'name'              => OpenAiEnum::GPT4_0613,
                'status'            => 0,
                'alias'             => '',
                'balance'            => '',
                'member_free'       => 0,
            ],
            OpenAiEnum::GPT4_1106_PREVIEW    => [
                'chat_key'             => self::OPEN_GPT_40,
                'key'               => OpenAiEnum::GPT4_1106_PREVIEW,
                'name'              => OpenAiEnum::GPT4_1106_PREVIEW,
                'status'            => 0,
                'alias'             => '',
                'balance'           => '',
                'member_free'       => 0,
            ],
            OpenAiEnum::GPT4_32k    => [
                'chat_key'             => self::OPEN_GPT_40,
                'key'               => OpenAiEnum::GPT4_32k,
                'name'              => OpenAiEnum::GPT4_32k,
                'status'            => 0,
                'alias'             => '',
                'balance'           => '',
                'member_free'       => 0,
            ],
            OpenAiEnum::GPT4_32k_0314    => [
                'chat_key'             => self::OPEN_GPT_40,
                'key'               => OpenAiEnum::GPT4_32k_0314,
                'name'              => OpenAiEnum::GPT4_32k_0314,
                'status'            => 0,
                'alias'             => '',
                'balance'           => '',
                'member_free'       => 0,
            ],
            //openai3.5-turbo
            Api2dEnum::getAliasNameList(Api2dEnum::GPT35_TURBO)   => [
                'chat_key'             => self::API2D_35,
                'key'               => Api2dEnum::getAliasNameList(Api2dEnum::GPT35_TURBO),
                'name'              => Api2dEnum::getAliasNameList(Api2dEnum::GPT35_TURBO),
                'status'            => 0,
                'alias'             => '',
                'balance'           => '',
                'member_free'       => 0,
            ],
            //openai3.5-turbo
            Api2dEnum::getAliasNameList(Api2dEnum::GPT35_TURBO_0301)    => [
                'chat_key'             => self::API2D_35,
                'key'               => Api2dEnum::getAliasNameList(Api2dEnum::GPT35_TURBO_0301),
                'name'              => Api2dEnum::getAliasNameList(Api2dEnum::GPT35_TURBO_0301),
                'status'            => 0,
                'alias'             => '',
                'balance'           => '',
                'member_free'       => 0,
            ],
            //openai3.5-turbo
            Api2dEnum::getAliasNameList(Api2dEnum::GPT35_TURBO_0613)    => [
                'chat_key'             => self::API2D_35,
                'key'               => Api2dEnum::getAliasNameList(Api2dEnum::GPT35_TURBO_0613),
                'name'              => Api2dEnum::getAliasNameList(Api2dEnum::GPT35_TURBO_0613),
                'status'            => 0,
                'alias'             => '',
                'balance'           => '',
                'member_free'       => 0,
            ],
            //openai3.5-turbo
            Api2dEnum::getAliasNameList(Api2dEnum::GPT35_TURBO_1106)    => [
                'chat_key'             => self::API2D_35,
                'key'               => Api2dEnum::getAliasNameList(Api2dEnum::GPT35_TURBO_1106),
                'name'              => Api2dEnum::getAliasNameList(Api2dEnum::GPT35_TURBO_1106),
                'status'            => 0,
                'alias'             => '',
                'balance'           => '',
                'member_free'       => 0,
            ],
            //openai3.5-turbo
            Api2dEnum::getAliasNameList(Api2dEnum::GPT35_TURBO_16K)    => [
                'chat_key'             => self::API2D_35,
                'key'               => Api2dEnum::getAliasNameList(Api2dEnum::GPT35_TURBO_16K),
                'name'              => Api2dEnum::getAliasNameList(Api2dEnum::GPT35_TURBO_16K),
                'status'            => 0,
                'alias'             => '',
                'balance'           => '',
                'member_free'       => 0,
            ],
            Api2dEnum::getAliasNameList(Api2dEnum::GPT35_TURBO_16K_0613)    => [
                'chat_key'             => self::API2D_35,
                'key'               => Api2dEnum::getAliasNameList(Api2dEnum::GPT35_TURBO_16K_0613),
                'name'              => Api2dEnum::getAliasNameList(Api2dEnum::GPT35_TURBO_16K_0613),
                'status'            => 0,
                'alias'             => '',
                'balance'           => '',
                'member_free'       => 0,
            ],
            Api2dEnum::getAliasNameList(Api2dEnum::GPT4)    => [
                'chat_key'             => self::API2D_40,
                'key'               => Api2dEnum::getAliasNameList(Api2dEnum::GPT4),
                'name'              => Api2dEnum::getAliasNameList(Api2dEnum::GPT4),
                'status'            => 0,
                'alias'             => '',
                'balance'           => '',
                'member_free'       => 0,
            ],
            Api2dEnum::getAliasNameList(Api2dEnum::GPT4_0314)    => [
                'chat_key'             => self::API2D_40,
                'key'               => Api2dEnum::getAliasNameList(Api2dEnum::GPT4_0314),
                'name'              => Api2dEnum::getAliasNameList(Api2dEnum::GPT4_0314),
                'status'            => 0,
                'alias'             => '',
                'balance'           => '',
                'member_free'       => 0,
            ],
            Api2dEnum::getAliasNameList(Api2dEnum::GPT4_0613)    => [
                'chat_key'             => self::API2D_40,
                'key'               => Api2dEnum::getAliasNameList(Api2dEnum::GPT4_0613),
                'name'              => Api2dEnum::getAliasNameList(Api2dEnum::GPT4_0613),
                'status'            => 0,
                'alias'             => '',
                'balance'           => '',
                'member_free'       => 0,
            ],
            Api2dEnum::getAliasNameList(Api2dEnum::GPT4_1106_PREVIEW)    => [
                'chat_key'             => self::API2D_40,
                'key'               => Api2dEnum::getAliasNameList(Api2dEnum::GPT4_1106_PREVIEW),
                'name'              => Api2dEnum::getAliasNameList(Api2dEnum::GPT4_1106_PREVIEW),
                'status'            => 0,
                'alias'             => '',
                'balance'           => '',
                'member_free'       => 0,
            ],
            Api2dEnum::getAliasNameList(Api2dEnum::GPT4_32k)    => [
                'chat_key'             => self::API2D_40,
                'key'               => Api2dEnum::getAliasNameList(Api2dEnum::GPT4_32k),
                'name'              => Api2dEnum::getAliasNameList(Api2dEnum::GPT4_32k),
                'status'            => 0,
                'alias'             => '',
                'balance'           => '',
                'member_free'       => 0,
            ],
            Api2dEnum::getAliasNameList(Api2dEnum::GPT4_32k_0314)    => [
                'chat_key'             => self::API2D_40,
                'key'               => Api2dEnum::getAliasNameList(Api2dEnum::GPT4_32k_0314),
                'name'              => Api2dEnum::getAliasNameList(Api2dEnum::GPT4_32k_0314),
                'status'            => 0,
                'alias'             => '',
                'balance'           => '',
                'member_free'       => 0,
            ],
            //科大讯飞1.5
            XingHuoEnum::XINGHUO15    => [
                'chat_key'             => self::XINGHUO,
                'key'               => XingHuoEnum::XINGHUO15,
                'name'              => XingHuoEnum::getAliasNameList(XingHuoEnum::XINGHUO15),
                'status'            => 0,
                'alias'             => '',
                'balance'           => '',
                'member_free'       => 0,
            ],
            //科大讯飞2.0
            XingHuoEnum::XINGHUO20    => [
                'chat_key'             => self::XINGHUO,
                'key'               => XingHuoEnum::XINGHUO20,
                'name'              => XingHuoEnum::getAliasNameList(XingHuoEnum::XINGHUO20),
                'status'            => 0,
                'alias'             => '',
                'balance'           => '',
                'member_free'       => 0,
            ],
            XingHuoEnum::XINGHUO30    => [
                'chat_key'             => self::XINGHUO,
                'key'               => XingHuoEnum::XINGHUO30,
                'name'              => XingHuoEnum::getAliasNameList(XingHuoEnum::XINGHUO30),
                'status'            => 0,
                'alias'             => '',
                'balance'           => '',
                'member_free'       => 0,
            ],
            XingHuoEnum::XINGHUO35    => [
                'chat_key'             => self::XINGHUO,
                'key'               => XingHuoEnum::XINGHUO35,
                'name'              => XingHuoEnum::getAliasNameList(XingHuoEnum::XINGHUO35),
                'status'            => 0,
                'alias'             => '',
                'balance'           => '',
                'member_free'       => 0,
            ],
            //百度文心一言
            WenXinEnum::ERNIE_BOT    => [
                'chat_key'             => self::WENXIN,
                'key'               => WenXinEnum::ERNIE_BOT,
                'name'              => WenXinEnum::ERNIE_BOT,
                'status'            => 0,
                'alias'             => '',
                'balance'           => '',
                'member_free'       => 0,
            ],
            WenXinEnum::ERNIE_BOT_TURBO    => [
                'chat_key'             => self::WENXIN,
                'key'               => WenXinEnum::ERNIE_BOT_TURBO,
                'name'              => WenXinEnum::ERNIE_BOT_TURBO,
                'status'            => 0,
                'alias'             => '',
                'balance'           => '',
                'member_free'       => 0,
            ],
            WenXinEnum::ERNIE_BOT4    => [
                'chat_key'             => self::WENXIN,
                'key'               => WenXinEnum::ERNIE_BOT4,
                'name'              => WenXinEnum::ERNIE_BOT4,
                'status'            => 0,
                'alias'             => '',
                'balance'           => '',
                'member_free'       => 0,
            ],
            //通义千问
            QwenEnum::QWEN_TURBO    => [
                'chat_key'             => self::QWEN,
                'key'               => QwenEnum::QWEN_TURBO,
                'name'              => QwenEnum::QWEN_TURBO,
                'status'            => 0,
                'alias'             => '',
                'balance'           => '',
                'member_free'       => 0,
            ],
            QwenEnum::QWEN_PLUS    => [
                'chat_key'             => self::QWEN,
                'key'               => QwenEnum::QWEN_PLUS,
                'name'              => QwenEnum::QWEN_PLUS,
                'status'            => 0,
                'alias'             => '',
                'balance'           => '',
                'member_free'       => 0,
            ],
            QwenEnum::QWEN_MAX    => [
                'chat_key'               => self::QWEN,
                'key'               => QwenEnum::QWEN_MAX,
                'name'              => QwenEnum::QWEN_MAX,
                'status'            => 0,
                'alias'             => '',
                'balance'           => '',
                'member_free'       => 0,
            ],
            QwenEnum::QWEN_MAX_1201    => [
                'chat_key'               => self::QWEN,
                'key'               => QwenEnum::QWEN_MAX_1201,
                'name'              => QwenEnum::QWEN_MAX_1201,
                'status'            => 0,
                'alias'             => '',
                'balance'           => '',
                'member_free'       => 0,
            ],
            QwenEnum::QWEN_MAX_LONGCONTEXT    => [
                'chat_key'               => self::QWEN,
                'key'               => QwenEnum::QWEN_MAX_LONGCONTEXT,
                'name'              => QwenEnum::QWEN_MAX_LONGCONTEXT,
                'status'            => 0,
                'alias'             => '',
                'balance'           => '',
                'member_free'       => 0,
            ],
            //腾讯混元
            HunYuanEnum::HUNYUAN_STD    => [
                'chat_key'               => self::HUNYUAN,
                'key'               => HunYuanEnum::HUNYUAN_STD,
                'name'              => HunYuanEnum::HUNYUAN_STD,
                'status'            => 0,
                'alias'             => '',
                'balance'           => '',
                'member_free'       => 0,
            ],
            HunYuanEnum::HUNYUAN_PRO    => [
                'chat_key'               => self::HUNYUAN,
                'key'               => HunYuanEnum::HUNYUAN_PRO,
                'name'              => HunYuanEnum::HUNYUAN_PRO,
                'status'            => 0,
                'alias'             => '',
                'balance'           => '',
                'member_free'       => 0,
            ],
            //AzureOpenAI
            AzureOpenAIEnum::AZURE_GPT35_TURBO    => [
                'chat_key'               => self::AZURE_OPEN_GPT_35,
                'key'               => AzureOpenAIEnum::AZURE_GPT35_TURBO,
                'name'              => AzureOpenAIEnum::AZURE_GPT35_TURBO,
                'status'            => 0,
                'alias'             => '',
                'balance'           => '',
                'member_free'       => 0,
            ],
            AzureOpenAIEnum::AZURE_GPT35_TURBO_16K    => [
                'chat_key'               => self::AZURE_OPEN_GPT_35,
                'key'               => AzureOpenAIEnum::AZURE_GPT35_TURBO_16K,
                'name'              => AzureOpenAIEnum::AZURE_GPT35_TURBO_16K,
                'status'            => 0,
                'alias'             => '',
                'balance'           => '',
                'member_free'       => 0,
            ],
            AzureOpenAIEnum::AZURE_GPT4    => [
                'chat_key'               => self::AZURE_OPEN_GPT_40,
                'key'               => AzureOpenAIEnum::AZURE_GPT4,
                'name'              => AzureOpenAIEnum::AZURE_GPT4,
                'status'            => 0,
                'alias'             => '',
                'balance'           => '',
                'member_free'       => 0,
            ],
            AzureOpenAIEnum::AZURE_GPT4_32k    => [
                'chat_key'               => self::AZURE_OPEN_GPT_40,
                'key'               => AzureOpenAIEnum::AZURE_GPT4_32k,
                'name'              => AzureOpenAIEnum::AZURE_GPT4_32k,
                'status'            => 0,
                'alias'             => '',
                'balance'           => '',
                'member_free'       => 0,
            ],
            AzureOpenAIEnum::AZURE_GPT4_VISION_PREVIEW    => [
                'chat_key'               => self::AZURE_OPEN_GPT_40,
                'key'               => AzureOpenAIEnum::AZURE_GPT4_VISION_PREVIEW,
                'name'              => AzureOpenAIEnum::AZURE_GPT4_VISION_PREVIEW,
                'status'            => 0,
                'alias'             => '',
                'balance'           => '',
                'member_free'       => 0,
            ],
            //minimax
            MiniMaxEnum::ABAB55             => [
                'chat_key'              => self::MINIMAX,
                'key'                   => MiniMaxEnum::ABAB55,
                'name'                  => MiniMaxEnum::ABAB55,
                'status'                => 0,
                'alias'                 => '',
                'balance'               => '',
                'member_free'           => 0,
            ],
            //gemini
            GeminiEnum::GEMINI_PRO      => [
                'chat_key'              => self::GEMINI,
                'key'                   => GEMINIEnum::GEMINI_PRO,
                'name'                  => GEMINIEnum::GEMINI_PRO,
                'status'                => 0,
                'alias'                 => '',
                'balance'               => '',
                'member_free'           => 0,
            ],
        ];
        if(true === $from){
            return $desc;
        }
        return $desc[$from] ?? [];

    }

    /**
     * @notes 获取模型指令
     * @param $from
     * @param $config
     * @return string|string[]
     * @author cjhao
     * @date 2024/1/11 11:15
     */
    public static function getPromptModel($from = true,$config = false)
    {

        $desc = [
            self::ZHIPUGLM          => self::getChatName(self::ZHIPUGLM),
            self::OPEN_GPT_35       => self::getChatName(self::OPEN_GPT_35),
            self::OPEN_GPT_40       => self::getChatName(self::OPEN_GPT_40),
            self::API2D_35          => self::getChatName(self::API2D_35),
            self::API2D_40          => self::getChatName(self::API2D_40),
            self::WENXIN            => self::getChatName(self::WENXIN),
            self::QWEN              => self::getChatName(self::QWEN),
            self::AZURE_OPEN_GPT_35 => self::getChatName(self::AZURE_OPEN_GPT_35),
            self::AZURE_OPEN_GPT_40 => self::getChatName(self::AZURE_OPEN_GPT_40),
            self::MINIMAX           => self::getChatName(self::MINIMAX),
            self::XINGHUO           => self::getChatName(self::XINGHUO),
        ];
        //获取模型，直接返回
        if(false === $config){
            if(true === $from){
                return $desc;
            }
            return $desc[$from] ?? '';
        }
        //获取模型配置
        $configLists = [];
        foreach ($desc as $chatKey => $model){
            $configLists[$chatKey] = '';
        }
        if(true === $config){
            return $configLists;
        }
        return $configLists[$from] ?? '';
    }
}
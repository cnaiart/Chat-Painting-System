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
namespace app\adminapi\logic\setting;
use app\common\{cache\KeyPoolCache,
    enum\chat\Api2dEnum,
    enum\chat\ChatEnum,
    logic\BaseLogic,
    service\chat\AiChatService,
    service\ConfigService};

/**
 * 对话配置逻辑列
 * Class ChatSettingLogic
 * @package app\adminapi\logic\setting
 */
class ChatSettingLogic extends BaseLogic
{


    /**
     * @notes 获取ai聊天配置列表
     * @param int $type
     * @return array
     * @author cjhao
     * @date 2023/6/13 14:50
     */
    public function getChatConfig()
    {
        $defaultConfigLists = ChatEnum::getDefaultChatConfig();
        $type = ConfigService::get('chat_config',  'type',ChatEnum::ZHIPUGLM);
        $chatConfigLists   = ConfigService::get('chat_config');
        $configLists = [];
        foreach ($defaultConfigLists as $key => $defaultConfig)
        {
            //数据库配置
            $chatConfig = $chatConfigLists[$key] ?? [];
            $chatConfig['status'] = $key == $type ? 1 : 0;
            unset($chatConfig['model_list']);
            $defaultConfig = array_merge($defaultConfig,$chatConfig);
            //转换数据类型
            $defaultConfig['status'] = (int)$defaultConfig['status'];
            $defaultConfig['context_num'] = (int)$defaultConfig['context_num'];
            $defaultConfig['temperature'] = (float)$defaultConfig['temperature'];
            $defaultConfig['top_p'] = (float)$defaultConfig['top_p'];
            $defaultConfig['presence_penalty'] = floatval($defaultConfig['presence_penalty'] ?? '');
            $defaultConfig['frequency_penalty'] = floatval($defaultConfig['frequency_penalty'] ?? '');
            $defaultConfig['n'] = intval($defaultConfig['n'] ?? '');
            //需要处理数据的key
            $disposeModel = [
                ChatEnum::QWEN,
                ChatEnum::XINGHUO,
                ChatEnum::HUNYUAN,
                ChatEnum::ZHIPUGLM,
                ChatEnum::WENXIN,
                ChatEnum::MINIMAX,
                ChatEnum::GEMINI
            ];
            if(in_array($key,$disposeModel)){
                $defaultConfig['presence_penalty'] = '';
                $defaultConfig['frequency_penalty'] = '';
                $defaultConfig['n'] = '';
                $defaultConfig['top_p'] = '';
            }
            //模型名称读取默认数据
            $defaultConfig['name'] = ChatEnum::getChatName($key);
            $configLists[$key] = $defaultConfig;

        }
        return [
            'config_lists'   => $configLists,
        ];
    }


    /**
     * @notes 设置ai参数配置
     * @param array $params
     * @return bool
     * @author cjhao
     * @date 2023/4/21 15:08
     */
    public function setChatConfig(array $params)
    {
        unset($params['model_list']);
        ConfigService::set('chat_config',$params['key'],$params);
        if(1 == $params['status']){
            ConfigService::set('chat_config','type',$params['key']);
            $billingConfigLists = ConfigService::get('chat_config','billing_config',[]);
            if(empty($billingConfigLists)){
                $billingConfigLists = ChatEnum::getDefaultBillingConfig();
            }
            $model = $params['model'];
            if(in_array($params['key'],[ChatEnum::API2D_35,ChatEnum::API2D_40])){
                $model = Api2dEnum::getAliasNameList($params['model']);
            }
            $modelBillingConfig = $billingConfigLists[$model] ?? [];
            if(empty($modelBillingConfig)){
                $modelBillingConfig = ChatEnum::getDefaultBillingConfig($model);
                $modelBillingConfig['alias'] = $modelBillingConfig['name'];
                $modelBillingConfig['balance'] = 1;
            }
            //开启选中的模型
            $modelBillingConfig['status'] = 1;
            $billingConfigLists[$model] = $modelBillingConfig;
            ConfigService::set('chat_config','billing_config',$billingConfigLists);
        }
        (new KeyPoolCache($params['key']))->delKey();
        return true;
    }


    /**
     * @notes 获取对话模型计费配置
     * @return array
     * @author cjhao
     * @date 2023/7/18 18:21
     */
    public function getChatBillingConfig()
    {
        $isOpen = ConfigService::get('chat_config','billing_is_open');
        $billingConfigLists = ConfigService::get('chat_config','billing_config',[]);
        $billingConfigMemberFree = array_column($billingConfigLists,'member_free');
        $memberModel = AiChatService::getChatOpenModel();
        $billingModelList = [];
        foreach ($billingConfigLists as $key => $billingConfigList){
            $defaultConfig = $defaultConfigLists[$key] ?? [];
            $billingConfig = array_merge($defaultConfig,$billingConfigList);
            $billingConfig['status'] = (int)$billingConfig['status'];
            $billingConfig['balance'] && $billingConfig['balance'] = (int)$billingConfig['balance'];
            //模型配置没有会员免费这个参数的时候默认模型会员免费
            $billingConfig['member_free'] = isset($billingConfig['member_free']) ? (int)$billingConfig['member_free'] : 0;
            if (!$billingConfigMemberFree && $memberModel === $billingConfig['key']) {
                $billingConfig['member_free'] = 1;
            }
            //模型名称读取默认数据
            $billingConfig['name'] = ChatEnum::getDefaultBillingConfig($billingConfig['key'])['name'];
            $billingModelList[] = $billingConfig;
        }
        return [
            'is_open'           => $isOpen,
            'member_model'      => ChatEnum::getChatModelLists($memberModel),
            'billing_config'    => $billingModelList,
            'chat_model_lists'  => ChatEnum::getDefaultBillingConfig()
        ];
    }


    /**
     * @notes 设置模型计费
     * @param array $post
     * @return string
     * @author cjhao
     * @date 2023/7/18 18:43
     */
    public function setChtBillingConfig(array $post)
    {
        $isOpen = $post['is_open'] ?? '';
        $billingConfig = $post['billing_config'] ?? [];
        $billingKeys = [];
        if('' === $isOpen){
            return '请选择模型计费状态';
        }
        if(!in_array($isOpen,[0,1])){
            return '模型计费状态错误';
        }
        if(empty($billingConfig)){
            return '请配置模型费用';
        }
        $billingConfigList = [];
        $chatModelKeys = array_keys(ChatEnum::getDefaultBillingConfig());
        foreach ($billingConfig as $config){
            if(!isset($config['key']) || empty($config['key'])){
                return '模型数据错误';
            }
            if(!in_array($config['key'],$chatModelKeys)){
                return '模型不存在';
            }
            if(in_array($config['key'],$billingKeys)){
                return '模型'.ChatEnum::getChatModelLists($config['key']).' 重复';
            }
            if(!in_array($config['status'],[0,1])){
                return '模型：'.ChatEnum::getChatModelLists($config['key']).' 状态错误';
            }
            $config['alias'] = '' === $config['alias'] ? ChatEnum::getChatModelLists($config['key']) : $config['alias'];
            $config['balance'] = '' === $config['balance'] ? 1 : $config['balance'];
            if($config['balance'] < 0){
                return '模型：'.ChatEnum::getChatModelLists($config['key']).' 消耗条数不能小于0';
            }
            if(!is_numeric($config['balance']) || false !== strpos($config['balance'], '.')){
                return '模型：'.ChatEnum::getChatModelLists($config['key']).' 消耗条数必须是整数';
            }
            if(!in_array($config['member_free'],[0,1])){
                return '模型：'.ChatEnum::getChatModelLists($config['key']).' 会员免费错误';
            }
            $billingConfigList[$config['key']] = $config;
            $billingKeys[] = $config['key'];
        }
        //验证必须开启对话模型
        if($isOpen){
            $statusSum = array_sum(array_column($billingConfig,'status'));
            if($statusSum <= 0){
                return '计费模型已开启，至少开启一个对话模型';
            }
        }
        ConfigService::set('chat_config','billing_is_open',$isOpen);
        ConfigService::set('chat_config','billing_config',$billingConfigList);
        return true;
    }



}
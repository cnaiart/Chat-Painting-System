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
use app\common\{enum\chat\ChatEnum,
    logic\BaseLogic,
    service\chat\AiChatService,
    service\ConfigService,
    service\FileService};

/**
 * AI设置逻辑类
 * Class AiSettingLogic
 * @package app\adminapi\logic\setting
 */
class AiSettingLogic extends BaseLogic
{
    /**
     * @notes 获取对话配置
     * @return array
     * @author ljj
     * @date 2023/5/25 2:54 下午
     */
    public function getChatConfig()
    {
        $chatLogo = ConfigService::get('default_image','chat_logo');
        $chatExample = ConfigService::get('default_image','chat_example');
        $chatTitleExample = ConfigService::get('default_image','chat_title_example');
        $result = [
//            'is_sensitive' => ConfigService::get('chat_config','is_sensitive',1),
            'is_markdown' => ConfigService::get('chat_config','is_markdown',1),
            'chat_logo' => ConfigService::get('chat_config','chat_logo',$chatLogo),
            'chat_default' => $chatLogo,
            'chat_example' => $chatExample,
//            'is_tip' => ConfigService::get('chat_config','is_tip',1),
//            'global_directives' => ConfigService::get('chat_config','global_directives',''),
            'chat_limit_tips' => ConfigService::get('chat_config','chat_limit_tips','今日对话次数已达上限'),
            'chat_title' => ConfigService::get('chat_config','chat_title'),
            'chat_title_example' => $chatTitleExample,
            'default_reply_open'     => ConfigService::get('chat_config','default_reply_open'),
            'default_reply'     => ConfigService::get('chat_config','default_reply'),
            'is_reopen'     => ConfigService::get('chat_config','is_reopen',0),
        ];
        $result['chat_logo'] = FileService::getFileUrl($result['chat_logo']);
        $result['chat_default'] = FileService::getFileUrl($result['chat_default']);
        $result['chat_example'] = FileService::getFileUrl($result['chat_example']);
        $result['chat_title_example'] = FileService::getFileUrl($result['chat_title_example']);

        $globalDirectives = AiChatService::getChatDirectives();
        $result['global_directives'] = $globalDirectives;
        $result['global_directives_model'] = ChatEnum::getPromptModel();


        return $result;
    }

    /**
     * @notes 设置对话配置
     * @param array $params
     * @return bool
     * @author ljj
     * @date 2023/5/25 3:05 下午
     */
    public function setChatConfig(array $params)
    {
//        ConfigService::set('chat_config','is_sensitive',$params['is_sensitive']);
        ConfigService::set('chat_config','is_markdown',$params['is_markdown']);
        ConfigService::set('chat_config','chat_logo',FileService::setFileUrl($params['chat_logo']));
//        ConfigService::set('chat_config','is_tip',$params['is_tip']);
        ConfigService::set('chat_config','global_directives',$params['global_directives']);
        ConfigService::set('chat_config','chat_limit_tips',$params['chat_limit_tips']);
        ConfigService::set('chat_config','chat_title',$params['chat_title']);
        ConfigService::set('chat_config','default_reply_open',$params['default_reply_open']);
        ConfigService::set('chat_config','default_reply',$params['default_reply']);
        ConfigService::set('chat_config','is_reopen',$params['is_reopen']);
        return true;
    }


    /**
     * @notes 获取联网配置
     * @return array
     * @author cjhao
     * @date 2023/12/22 18:31
     */
    public function getNetworkConfig():array
    {
        $isOpen = ConfigService::get('chat_config','network_is_open');
        $searchLimit = ConfigService::get('chat_config','search_limit');
        $networkApi = ConfigService::get('chat_config','network_api','');
        $networkBalance = ConfigService::get('chat_config','network_balance');
        $networkSystem = ConfigService::get('chat_config','network_system');
        return [
            'network_is_open'       => $isOpen,
            'search_limit'          => $searchLimit,
            'network_api'           => $networkApi,
            'network_balance'       => $networkBalance,
            'network_system'        => $networkSystem,
        ];
    }

    /**
     * @notes 设置联网配置
     * @param array $params
     * @return string|bool
     * @author cjhao
     * @date 2023/12/22 18:39
     */
    public function setNetworkConfig(array $params):string|bool
    {
        $isOpen = $params['network_is_open'] ?? '';
        $searchLimit = $params['search_limit'] ?? '';
        $networkApi = $params['network_api'] ?? '';
        $networkBalance = $params['network_balance'] ?? '';
        $networkSystem = $params['network_system'] ?? '';
        if('' === $isOpen || !in_array($isOpen,[0,1])){
            return '请选择联网功能状态';
        }
        if('' === $searchLimit || $searchLimit < 1 || $searchLimit > 30){
            return '搜索条数在1~30';
        }
        if($networkBalance < 0){
            return '额外扣费不能小于0';
        }
        ConfigService::set('chat_config','network_is_open',$isOpen);
        ConfigService::set('chat_config','search_limit',(int)$searchLimit);
        ConfigService::set('chat_config','network_api',$networkApi);
        ConfigService::set('chat_config','network_balance',$networkBalance);
        ConfigService::set('chat_config','network_system',$networkSystem);
        return  true;
    }

}
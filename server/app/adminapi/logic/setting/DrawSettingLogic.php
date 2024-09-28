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
use app\common\{
    enum\DrawEnum,
    logic\BaseLogic,
    enum\chat\ChatEnum,
    service\ConfigService,
    enum\qrcode\QrcodeEnum,
};
use think\facade\Validate;

/**
 * 绘画配置逻辑类
 * Class DrawSettingLogic
 * @package app\adminapi\logic\setting
 */
class DrawSettingLogic  extends BaseLogic
{
    /**
     * @notes 绘画配置
     * @return array
     * @author 段誉
     * @date 2023/7/7 14:33
     */
    public function getDrawConfig()
    {
        // 绘画总开关
        $isOpen = ConfigService::get('draw_config', 'is_open', 0);
//        // 免责声明
//        $disclaimerStatus = ConfigService::get('draw_config', 'disclaimer_status', 0);
//        $disclaimerContent = ConfigService::get('draw_config', 'disclaimer_content', "");
        // 超时处理时长
        $timeOut = ConfigService::get('draw_config', 'time_out', 10);

        // 默认配置
        $defaultConfigLists = DrawEnum::getDrawDefaultConfig();
        // 选中类型
        $selectedType = ConfigService::get('draw_config',  'type',DrawEnum::API_ZHISHUYUN_FAST);
        // 已保存配置
        $configLists  = ConfigService::get('draw_config');
        // 返回数据
        $resultConfig = [];

        // 旧版本数据
        $oldConfigType = ConfigService::get('draw_config', 'api_type', "zhishuyun_fast");
        $oldConfigToken = ConfigService::get('draw_config', 'api_token', "");

        foreach ($defaultConfigLists as $key => $defaultConfig) {
            $drawConfig = $configLists[$key] ?? [];
            // 兼容旧版本数据
            if (empty($drawConfig) && $key == $oldConfigType) {
                $drawConfig['token'] = $oldConfigToken;
            }
            $drawConfig['type'] = $key;
            $drawConfig['status'] = $key == $selectedType ? 1 : 0;
            $itemConfig = array_merge($defaultConfig, $drawConfig);
            $itemConfig['auto_translate'] = (int)$itemConfig['auto_translate'];
            $itemConfig['status'] = (int)$itemConfig['status'];
            $itemConfig['translate_type'] = (int)$itemConfig['translate_type'];

            if (!empty($itemConfig['name']) && $itemConfig['name'] == '码多多-MJ') {
                $itemConfig['name'] = '官方直连-MJ';
            }

            if (!empty($itemConfig['name']) && $itemConfig['name'] == '知数云-MJ') {
                $itemConfig['name'] = '知数云-快速MJ';
            }

            $resultConfig[$key] = $itemConfig;
        }
        //翻译模型
        $translateModelLists = [
            ChatEnum::OPEN_GPT_35 => ChatEnum::getChatName(ChatEnum::OPEN_GPT_35),
            ChatEnum::OPEN_GPT_40 => ChatEnum::getChatName(ChatEnum::OPEN_GPT_40),
            ChatEnum::API2D_35 => ChatEnum::getChatName(ChatEnum::API2D_35),
            ChatEnum::API2D_40 => ChatEnum::getChatName(ChatEnum::API2D_40),
            ChatEnum::WENXIN => ChatEnum::getChatName(ChatEnum::WENXIN),
        ];
        //绘画翻译配置
        $translateConfig  = ConfigService::get('draw_config','translate',DrawEnum::getTranslateConfig());
        return [
            // 是否开启绘画功能
            'is_open'                   => $isOpen,
            // 免责声明
//            'disclaimer_status'         => $disclaimerStatus,
//            'disclaimer_content'        => $disclaimerContent,
            // 配置列表
            'config_lists'              => $resultConfig,
            //翻译模型
            'translate_model_lists'     => $translateModelLists,
            //翻译配置
            'translate_config'          => $translateConfig,
            // 超时时长
            'time_out'                  => $timeOut,
        ];
    }

    /**
     * @notes 绘画配置
     * @param array $params
     * @return bool
     * @author 段誉
     * @date 2023/6/21 9:48
     */
    public function setDrawConfig(array $params)
    {
        //绘画话配置
        $drawConfig = $params['draw_config'];
        if(DrawEnum::API_MDDAI_MJ == $drawConfig['type'] && $drawConfig['proxy_wss'] && 'wss'!= substr($drawConfig['proxy_wss'], 0, 3)){
            return 'WSS域名必须是wss开头';
        }
        //翻译配置
        $translateConfig = $params['translate_config'] ?? [];
        // 绘画超时处理时间
        $timeOut = $drawConfig['time_out'] ?? 10;

        if (!isset($drawConfig['is_open']) || !in_array($drawConfig['is_open'], [0, 1])) {
            return '请选择绘画功能开关';
        }

        if (empty($drawConfig['type'])) {
            return '请选择ai绘画接口';
        }

        if($timeOut < 0) {
            return '超时处理时长不能小于0';
        }

        if (!isset($drawConfig['status']) || !in_array($drawConfig['status'], [0, 1])) {
            return '参数缺失';
        }
        ConfigService::set('draw_config', 'is_open', $drawConfig['is_open']);
        unset($drawConfig['is_open']);

        // 免责声明
        if (isset($drawConfig['disclaimer_status']) && in_array($drawConfig['disclaimer_status'], [0, 1])) {
            ConfigService::set('draw_config', 'disclaimer_status', $drawConfig['disclaimer_status']);
            unset($drawConfig['disclaimer_status']);
        }
        if (!empty($drawConfig['disclaimer_content'])) {
            ConfigService::set('draw_config', 'disclaimer_content', $drawConfig['disclaimer_content']);
            unset($drawConfig['disclaimer_content']);
        }

        ConfigService::set('draw_config',$drawConfig['type'], $drawConfig);
        if(1 == $drawConfig['status']) {
            ConfigService::set('draw_config','type', $drawConfig['type']);
        }
        if($drawConfig['auto_translate']){
            if(empty($translateConfig)){
                return '请填写翻译配置';
            }
            if(empty($translateConfig['prompt'])){
                return '请输入翻译指令';
            }
//            if(!strpos($translateConfig['prompt'], '${prompt}')){
//                return '翻译指令必须填写一个变量';
//            }

        }
        //配置绘画配置
        ConfigService::set('draw_config','translate', $translateConfig);

        // 超时处理时长
        ConfigService::set('draw_config','time_out', $timeOut);

        return true;
    }



    /**
     * @notes 获取绘画计费模型配置
     * @return array
     * @author cjhao
     * @date 2023/7/18 19:00
     */
    public function getDrawBillingConfig(){

        $isOpen = ConfigService::get('draw_config','billing_is_open');
        $billingConfig = ConfigService::get('draw_config','billing_config',[]);
        $defaultConfigLists = DrawEnum::getDefaultBillingConfig();

        $billingModelList = [];
        foreach ($billingConfig as $key => $config){
            $defaultConfig = $defaultConfigLists[$key] ?? [];
            unset($defaultConfigLists[$key]);

            $billingConfig = array_merge($defaultConfig, $config);
            $billingConfig['status'] = (int)$config['status'];
            $billingConfig['member_free'] = (int)($config['member_free'] ?? 0);
            $config['balance'] && $billingConfig['balance'] = (int)$config['balance'];

            if (!empty($config['name']) && $config['name'] == '码多多-MJ') {
                $billingConfig['name'] = '官方直连-MJ';
            }

            if (!empty($config['alias']) && $config['alias'] == '码多多-MJ') {
                $billingConfig['alias'] = '官方直连-MJ';
            }

            if (!empty($config['name']) && $config['name'] == '知数云-MJ') {
                $billingConfig['name'] = '知数云-快速MJ';
            }

            if (!empty($config['alias']) && $config['alias'] == '知数云-MJ') {
                $billingConfig['alias'] = '知数云-快速MJ';
            }

            $billingModelList[] = $billingConfig;
        }

        $billingModelList = array_merge($billingModelList, array_values($defaultConfigLists));

        $defaultModel = ConfigService::get('draw_config', 'type', DrawEnum::API_ZHISHUYUN_FAST);
        $defaultModelName = DrawEnum::getDrawDefaultConfig($defaultModel)['name'] ?? $defaultModel;

        return [
            'is_open'           => $isOpen,
            'default_model'     => $defaultModelName,
            'billing_config'    => $billingModelList,
        ];
    }

    /**
     * @notes 设置绘画模型计费
     * @param array $post
     * @return string
     * @author cjhao
     * @date 2023/7/18 18:43
     */
    public function setDrawBillingConfig(array $post)
    {
        $isOpen = $post['is_open'] ?? '';
        $billingConfig = $post['billing_config'] ?? [];
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
        foreach ($billingConfig as &$config){
            if(!isset($config['key']) || empty($config['key'])){
                return '模型数据错误';
            }
            if(!in_array($config['status'],[0,1])){
                return '模型：'.DrawEnum::getDefaultBillingConfig($config['key'])['name'].' 状态错误';
            }

            $config['alias'] = '' === $config['alias'] ? DrawEnum::getDefaultBillingConfig($config['key'])['name'] : $config['alias'];
            $config['balance'] = '' === $config['balance'] ? 1 : $config['balance'];
            if($config['balance'] < 0){
                return '模型：'.DrawEnum::getDefaultBillingConfig($config['key'])['name'].' 消耗条数不能小于0';
            }
            if(!is_numeric($config['balance']) || false !== strpos($config['balance'], '.')){
                return '模型：'.DrawEnum::getDefaultBillingConfig($config['key'])['name'].' 消耗条数必须是整数';
            }
            $billingConfigList[$config['key']] = $config;
        }
        //验证必须开启对话模型
        if($isOpen){
            $statusSum = array_sum(array_column($billingConfig,'status'));
            if($statusSum <= 0){
                return '计费模型已开启，至少开启一个绘画模型';
            }
        }
        ConfigService::set('draw_config','billing_is_open',$isOpen);
        ConfigService::set('draw_config','billing_config',$billingConfigList);
        return true;
    }

    /**
     * @notes 艺术二维码配置
     * @return array
     * @author mjf
     * @date 2023/10/16 16:19
     */
    public function getQrcodeConfig()
    {
        // 总开关
        $isOpen = ConfigService::get('art_qrcode_config', 'is_open', 0);
        // 默认配置
        $defaultConfigLists = QrcodeEnum::getDefaultCodeConfig();
        // 选中api类型
        $selectedType = ConfigService::get('art_qrcode_config', 'type', QrcodeEnum::API_MEWX);
        // 已保存配置
        $configLists = ConfigService::get('art_qrcode_config');
        // 返回数据
        $resultConfig = [];

        foreach ($defaultConfigLists as $key => $defaultConfig) {
            $drawConfig = $configLists[$key] ?? [];
            $drawConfig['status'] = $key == $selectedType ? 1 : 0;
            $itemConfig = array_merge($defaultConfig, $drawConfig);
            $itemConfig['status'] = (int)$itemConfig['status'];
            $resultConfig[$key] = $itemConfig;
        }

        return [
            // 是否开启艺术二维码
            'is_open' => $isOpen,
            // 配置列表
            'config_lists' => $resultConfig,
        ];
    }

    /**
     * @notes 艺术二维码
     * @param array $params
     * @return bool|string
     * @author mjf
     * @date 2023/10/16 16:31
     */
    public function setQrcodeConfig(array $params)
    {
        if (!isset($params['is_open']) || !in_array($params['is_open'], [0, 1])) {
            return '请选择功能状态';
        }

        if (empty($params['type'])) {
            return '请选择ai接口';
        }

        if (!isset($params['status']) || !in_array($params['status'], [0, 1])) {
            return '请选择ai接口';
        }

        ConfigService::set('art_qrcode_config', 'is_open', $params['is_open']);
        unset($params['is_open']);

        ConfigService::set('art_qrcode_config', $params['type'], $params);
        if (1 == $params['status']) {
            ConfigService::set('art_qrcode_config', 'type', $params['type']);
        }

        return true;
    }


    /**
     * @notes 艺术二维码-计费模型
     * @return array
     * @author mjf
     * @date 2023/10/16 17:09
     */
    public function getQrcodeBillingConfig()
    {
        $isOpen = ConfigService::get('art_qrcode_config', 'billing_is_open', 0);
        $billingConfig = ConfigService::get('art_qrcode_config', 'billing_config', []);
        $defaultConfigLists = QrcodeEnum::getDefaultBillingConfig();

        $billingModelList = [];
        foreach ($billingConfig as $key => $config) {
            $defaultConfig = $defaultConfigLists[$key] ?? [];
            unset($defaultConfigLists[$key]);

            $billingConfig = array_merge($defaultConfig, $config);

            $billingConfig['member_free'] = (int)($config['member_free'] ?? 0);
            $billingConfig['status'] = (int)$config['status'];
            $config['balance'] && $billingConfig['balance'] = (int)$config['balance'];
            $billingModelList[] = $billingConfig;
        }

        $billingModelList = array_merge($billingModelList, array_values($defaultConfigLists));

        $defaultModel = ConfigService::get('art_qrcode_config', 'type', QrcodeEnum::API_MEWX);
        $defaultModelName = QrcodeEnum::getDefaultCodeConfig($defaultModel)['name'] ?? $defaultModel;

        return [
            'is_open' => $isOpen,
            'default_model' => $defaultModelName,
            'billing_config' => $billingModelList,
        ];
    }

    /**
     * @notes 艺术二维码-计费模型
     * @param array $post
     * @return bool|string
     * @author mjf
     * @date 2023/10/16 17:26
     */
    public function setQrcodeBillingConfig(array $post)
    {
        $isOpen = $post['is_open'] ?? '';
        $billingConfig = $post['billing_config'] ?? [];
        if ('' === $isOpen) {
            return '请选择模型计费状态';
        }
        if (!in_array($isOpen, [0, 1])) {
            return '模型计费状态错误';
        }
        if (empty($billingConfig)) {
            return '请配置模型费用';
        }
        $billingConfigList = [];
        foreach ($billingConfig as &$config) {
            if (!isset($config['key']) || empty($config['key'])) {
                return '模型数据错误';
            }
            if (!in_array($config['status'], [0, 1])) {
                return '模型：' . QrcodeEnum::getDefaultBillingConfig($config['key'])['name'] . ' 状态错误';
            }
            $config['alias'] = '' === $config['alias'] ? QrcodeEnum::getDefaultBillingConfig($config['key'])['name'] : $config['alias'];
            $config['balance'] = '' === $config['balance'] ? 1 : $config['balance'];
            if ($config['balance'] < 0) {
                return '模型：' . QrcodeEnum::getDefaultBillingConfig($config['key'])['name'] . ' 消耗条数不能小于0';
            }
            if (!is_numeric($config['balance']) || false !== strpos($config['balance'], '.')) {
                return '模型：' . QrcodeEnum::getDefaultBillingConfig($config['key'])['name'] . ' 消耗条数必须是整数';
            }
            $billingConfigList[$config['key']] = $config;
        }

        if ($isOpen) {
            $statusSum = array_sum(array_column($billingConfig, 'status'));
            if ($statusSum <= 0) {
                return '计费模型已开启，至少开启一个模型';
            }
        }
        ConfigService::set('art_qrcode_config', 'billing_is_open', $isOpen);
        ConfigService::set('art_qrcode_config', 'billing_config', $billingConfigList);
        return true;
    }

    /**
     * @notes 获取绘画设置
     * @return array
     * @author ljj
     * @date 2023/12/26 2:45 下午
     */
    public static function getDrawSetting()
    {
        return [
            // 允许用户分享：1-开启；0-关闭；
            'is_allow_share' => ConfigService::get('draw_square_config', 'is_allow_share', config('project.draw_square_config.is_allow_share')),
            // 自动通过审核：1-开启；0-关闭；
            'is_auto_pass' => ConfigService::get('draw_square_config', 'is_auto_pass', config('project.draw_square_config.is_auto_pass')),
            //分享奖励对话次数
            'chat_rewards' => ConfigService::get('draw_square_config','chat_rewards', config('project.draw_square_config.chat_rewards')),
            //分享奖励绘画次数
            'draw_rewards' => ConfigService::get('draw_square_config','draw_rewards', config('project.draw_square_config.draw_rewards')),
            //每天最多分享次数
            'max_share' => ConfigService::get('draw_square_config','max_share', config('project.draw_square_config.max_share')),
            // 免责声明
            'disclaimer_status' => ConfigService::get('draw_config', 'disclaimer_status', 0),
            'disclaimer_content' => ConfigService::get('draw_config', 'disclaimer_content', ""),
        ];
    }

    /**
     * @notes 设置绘画设置
     * @param $params
     * @return bool
     * @author ljj
     * @date 2023/12/26 2:45 下午
     */
    public static function setDrawSetting($params)
    {
        if (!isset($params['is_allow_share']) || !in_array($params['is_allow_share'],[0,1])) {
            return '允许用户分享值错误';
        }
        if (!isset($params['is_auto_pass']) || !in_array($params['is_auto_pass'],[0,1])) {
            return '自动通过审核值错误';
        }
        if (!isset($params['chat_rewards']) || !is_numeric($params['chat_rewards']) || $params['chat_rewards'] < 0) {
            return '奖励对话次数值错误';
        }
        if (!isset($params['draw_rewards']) || !is_numeric($params['draw_rewards']) || $params['draw_rewards'] < 0) {
            return '奖励绘画次数值错误';
        }
        if ($params['chat_rewards'] == 0 && $params['draw_rewards'] == 0) {
            return '对话次数和绘画次数至少有一个大于0';
        }
        if (!isset($params['max_share']) || !is_numeric($params['max_share']) || $params['max_share'] < 0) {
            return '每天最多分享次数值错误';
        }
        if (!isset($params['disclaimer_status']) || !in_array($params['disclaimer_status'],[0,1])) {
            return '免责声明值错误';
        }
        ConfigService::set('draw_square_config', 'is_allow_share', $params['is_allow_share']);
        ConfigService::set('draw_square_config', 'is_auto_pass', $params['is_auto_pass']);
        ConfigService::set('draw_square_config', 'chat_rewards', $params['chat_rewards']);
        ConfigService::set('draw_square_config', 'draw_rewards', $params['draw_rewards']);
        ConfigService::set('draw_square_config', 'max_share', $params['max_share']);
        // 免责声明
        ConfigService::set('draw_config', 'disclaimer_status', $params['disclaimer_status']);
        ConfigService::set('draw_config', 'disclaimer_content', $params['disclaimer_content']);
        return true;
    }
}
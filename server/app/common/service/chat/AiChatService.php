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
namespace app\common\service\chat;
use DfaFilter\SensitiveHelper;
use app\common\enum\{ContentCensorEnum, chat\Api2dEnum, chat\ChatEnum, ChatRecordEnum, DefaultEnum};
use app\common\{model\ChatRecords, model\user\User, logic\BaseLogic, model\SensitiveWord, service\ConfigService};
use think\Exception;
use WpOrg\Requests\{
    Requests,
    Exception as WpException
};


/**
 * ai统一逻辑服务类
 * Class AiChatService
 * @package app\common\service
 */
class AiChatService extends BaseLogic
{

    /**
     * @notes 获取各个模型的计费
     * @param int $userId
     * @param string $model true-返回全部模型
     * @return array
     * @author cjhao
     * @date 2023/7/19 12:15
     */
    public static function getbillingModel(int $userId,$model = true):array
    {
        $type = self::getChatOpenModel();
        $isOpen = ConfigService::get('chat_config','billing_is_open');
        $billingModelLists = [];
        //判断用户当前是否属于会员
        $user = (new User())->append(['member_info'])->findOrEmpty($userId);
        $now = time();
        $userMember = false;
        if($user->member_perpetual || ($user->member_end_time && $user->member_end_time > $now)){
            $userMember = true;
        }
        //计费模型没开启，系统默认模型一条
        if(0 == $isOpen){
            $billingModelLists = ChatEnum::getDefaultBillingConfig();
            $balance = 1;
            $memberFree = false;
            //会员不消耗次数
            if($userMember){
                $balance = 0;
                $memberFree = true;
            }
            return $billingModelLists[$type] = [
                'key'           => $type,
                'alias'         => $type,
                'balance'       => (int)$balance,
                'default'       => true,
                'member_free'   => $memberFree,
            ];
        }

        $billingConfig = ConfigService::get('chat_config','billing_config');
        $billingConfigMemberFree = array_column($billingConfig,'member_free');
        foreach ($billingConfig as $config){
            if( 0 == $config['status']){
                continue;
            }
            //模型配置没有会员免费这个参数的时候默认模型会员免费
            if (!$billingConfigMemberFree) {
                if ($type === $config['key']) {
                    $config['member_free'] = 1;
                } else {
                    $config['member_free'] = 0;
                }
            }
            $config['member_free'] = (int)$config['member_free'];
            $default = false;
            $memberFree = false;
            $balance = $config['balance'];
            if($type === $config['key']){
                $default = true;
            }
            //会员不消耗次数
            if($userMember && $config['member_free']){
                //会员是否已达到套餐限制对话次数
                $todayChatNum = ChatRecords::where(['user_id'=>$userId])
                    ->whereDay('create_time')
                    ->count();
                if ($user['member_info']['chat_limit'] && $todayChatNum >= $user['member_info']['chat_limit']) {
                    $balance = $config['balance'];
                    $memberFree = false;
                } else {
                    $balance = 0;
                    $memberFree = true;
                }
            }
            $billingModelLists[$config['key']] = [
                'key'           => $config['key'],
                'alias'         => $config['alias'],
                'balance'       => (int)$balance,
                'default'       => $default,
                'member_free'   => $memberFree,
            ];
        }
        //如果只有一个模型，直接变成默认的
        if( 1 == count($billingModelLists)){
            $billingModelLists[array_keys($billingModelLists)[0]]['default'] = true;
        }
        if(true === $model){
            return $billingModelLists;
        }
        return $billingModelLists[$model] ?? [];
    }

    /**
     * @notes 审核全局指令
     * @param $messages
     * @author cjhao
     * @date 2023/6/21 15:14
     */
    public static function getGlobalDirectives(&$message,$source = 'openai')
    {

        //全局审核指令
        $globalDirectives = ConfigService::get('chat_config','global_directives','');
        if(!$globalDirectives){
            return ;
        }
        switch ($source){
            case 'openai':
                $message[] = ['role' => 'system', 'content' => $globalDirectives];
                break;

        }

    }

    /**
     * @notes 敏感词验证
     * @param string $content
     * @throws \DfaFilter\Exceptions\PdsBusinessException
     * @throws \DfaFilter\Exceptions\PdsSystemException
     * @throws \think\Exception
     * @author cjhao
     * @date 2023/6/21 10:42
     */
    public static function Sensitive(string $content):void
    {
        //敏感词过滤
        $isSensitive      = ConfigService::get('chat_config','is_sensitive',1); //默认开启
        if(!$isSensitive){
            return ;
        }
        //获取数据库敏感词
        $sensitiveWord = SensitiveWord::where(['status'=>1])->column('word');
        //一条数据可能含有多个敏感词，'；'分隔开
        $sensitive_arr = [];
        foreach ($sensitiveWord as $sensitiveWordValue) {
            $sensitive_arr = array_merge($sensitive_arr,explode('；',$sensitiveWordValue));
        }

        //读取敏感词文件
        //读取加密的密钥
        $file = fopen("../extend/sensitive_key.bin", "rb");
        $key = fread($file, 32);
        $iv = fread($file, 16);
        fclose($file);
        //读取加密的数据
        $ciphertext = file_get_contents("../extend/sensitive_data.bin");
        //使用 CBC 模式解密数据
        $plaintext = openssl_decrypt($ciphertext, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);

        //过滤敏感词
        $wordFilePath = explode(PHP_EOL,trim($plaintext));
        $sensitiveWord = array_merge($wordFilePath,$sensitive_arr);
        $sensitiveWordArr = array_chunk($sensitiveWord,20000,false);//拆分数组
        $sensitiveWordGroup = [];
        foreach ($sensitiveWordArr as $sensitiveWordArrValue) {
            $handle = SensitiveHelper::init()->setTree($sensitiveWordArrValue);
            $badWordList = $handle->getBadWord($content);// 获取内容中所有的敏感词
            $sensitiveWordGroup = array_merge($sensitiveWordGroup,$badWordList);
        }
        if (!empty($sensitiveWordGroup)) {
            throw new Exception('提问存在敏感词：'.implode(',',$sensitiveWordGroup));
        }

    }


    /**
     * @notes 问题审核
     * @param string $content
     * @param string $type chat=聊天  draw=绘画
     * @throws Exception
     * @author ljj
     * @date 2023/7/19 10:02 上午
     */
    public static function askCensor(string $content, string $contentType = ContentCensorEnum::TYPE_TEXT): void
    {
        $askOpen = ConfigService::get('content_censor', 'ask_open', 0);
        if (!$askOpen) {
            return;
        }
        self::contentCensor($content, $contentType);
    }


    /**
     * @notes 内容审核
     * @param string $content
     * @param string $contentType
     * @throws Exception
     */
    public static function contentCensor(string $content, string $contentType = ContentCensorEnum::TYPE_TEXT)
    {
        try {
            $APP_ID = ConfigService::get('content_censor','app_id');
            $API_KEY = ConfigService::get('content_censor','api_key');
            $SECRET_KEY = ConfigService::get('content_censor','secret_key');
            if (!$APP_ID || !$API_KEY || !$SECRET_KEY) {
                throw new Exception('内容审核配置缺失', 10006);
            }

            $client = new \aip\AipContentCensor($APP_ID, $API_KEY, $SECRET_KEY);

            if ($contentType == ContentCensorEnum::TYPE_IMAGE) {
                $result = $client->imageCensorUserDefined($content);
            } else {
                $result = $client->textCensorUserDefined($content);
            }

            if (isset($result['error_code'])) {
                throw new Exception($result['error_msg'] ?? '审核错误', 10006);
            }

            $data = [];
            if (isset($result['conclusionType']) && $result['conclusionType'] > ChatRecordEnum::CENSOR_STATUS_COMPLIANCE) {
                $dataList = $result['data'] ?? [];
                foreach ($dataList as $key => $val) {
                    $val['msg'] && $data[$key] = $val['msg'] . '：';

                    $hitsLists = $val['hits'] ?? [];
                    if (empty($hitsLists)) {
                        continue;
                    }
                    foreach ($hitsLists as $hits_val) {
                        if (isset($hits_val['words'])) {
                            $data[$key] .= implode('、', $hits_val['words']);
                        }
                    }
                }
            }
            if (!empty($data)) {
                throw new Exception(implode('、', $data), 10007);
            }
        } catch (Exception $e) {
            throw new Exception('百度内容审核:' . $e->getMessage(), $e->getCode());
        }
    }


    /**
     * @notes 返回流数据
     * @param string $event：事件
     * @param string $data：流数据
     * @param string $id：id
     * @param int $index：层级
     * @param string $model：模型类型
     * @author cjhao
     * @date 2023/6/21 18:21
     */
    public static function parseReturnStream(string $event,string $id,string $data,int $index,string $model,$incremental = true)
    {
        if(!env('APP_DEBUG')){
            $model = '';
        }
        $chatMessage = [
            'event'         => $event,
            'id'            => $id,
            'data'          => $data,
            'model'         => $model,
            'index'         => $index,
            'incremental'   => $incremental,//默认增量返回
        ];

        $jsonchatMessage = json_encode($chatMessage);
        echo "data:".$jsonchatMessage."\n\n";
        ob_flush();
        flush();
    }

    /**
     * @notes 返回错误流数据
     * @param string $event：事件
     * @param string $data：流数据
     * @param string $model：模型类型
     * @author cjhao
     * @date 2023/6/21 18:21
     */
    public static function parseReturnErrorStream(string $event,string $data,string $model,$code = 0)
    {
        if(!env('APP_DEBUG')){
            $model = '';
        }
        $chatMessage = [
            'event'         => $event,
            'id'            => '',
            'data'          => $data,
            'model'         => $model,
            'code'          => $code,
            'index'         => 0,
            'incremental'   => true,//默认增量返回
        ];

        $jsonchatMessage = json_encode($chatMessage);
        echo "data:".$jsonchatMessage."\n\n";
        ob_flush();
        flush();
        exit();
    }


    /**
     * @notes 获取当前开启的对话模型
     * @return mixed
     * @author cjhao
     * @date 2023/12/25 11:51
     */
    public static function getChatOpenModel()
    {

        $type = ConfigService::get('chat_config','type',ChatEnum::ZHIPUGLM);
        $defaultConfig = ChatEnum::getDefaultChatConfig($type);
        $config = ConfigService::get('chat_config',$type,[]);
        $config = array_merge($defaultConfig,$config);
        $model = $config['model'];
        if(in_array($config['key'],[ChatEnum::API2D_35,ChatEnum::API2D_40])){
            $model = Api2dEnum::getAliasNameList($model);
        }
        return $model;
    }

    /**
     * @notes 联网搜索数据
     * @param string $question
     * @return mixed|string|void
     * @author cjhao
     * @date 2023/12/25 15:32
     */
    public static function getNetworkData(string $question)
    {
        try {
            $isOpen = ConfigService::get('chat_config','network_is_open');
            //联网获取的调试
            $searchLimit = ConfigService::get('chat_config','search_limit',20);
            //联网api
            $networkApi = ConfigService::get('chat_config','network_api','https://lite.duckduckgo.com');
            if(empty($isOpen)){
                throw new Exception('联网功能未开启');
            }
            if(empty($networkApi)){
                throw new Exception('请联系管理员填写联网配置');
            }
            $networkApiParse = isDomainOnly($networkApi);
            if('' == $networkApiParse || '/' == $networkApiParse){
                if (preg_match('/\/$/', $networkApi)) {
                    $networkApi .= 'lite';
                } else {
                    $networkApi .= '/lite';
                }

            }
            //联网搜索
            $curl = curl_init();
            $url= $networkApi.'?q='.urlencode($question).'&df=&kl=&format=json';
            curl_setopt_array($curl, array(
                CURLOPT_URL =>$url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 20,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ));
            $response = curl_exec($curl);
            preg_match_all("/<td class=\'result-snippet\'>\s*(.*?)\s*<\/td>/", $response, $content);
            $contentLists = array_slice($content[1], 0, $searchLimit);
            $networkData = '';
            foreach ($contentLists as $content){
                $networkData .= $content.PHP_EOL;
            }
            return $networkData;
        }catch (Exception|WpException $e){
            throw new Exception("联网失败:".$e->getMessage());
        }
    }


    /**
     * @notes 获取全局指令
     * @return array|int|mixed|string
     * @author cjhao
     * @date 2024/1/11 11:36
     */
    public static function getChatDirectives($chatKey = true)
    {
        $defaultGlobalDirectives = ChatEnum::getPromptModel(true,true);
        $globalDirectives =  ConfigService::get('chat_config','global_directives',$defaultGlobalDirectives);
        if(is_array($globalDirectives)){
            $globalDirectives = array_merge($defaultGlobalDirectives,$globalDirectives);
        }
        if(!is_array($globalDirectives)){
            foreach ($defaultGlobalDirectives as $model => $directive){
                $defaultGlobalDirectives[$model] = $globalDirectives;
            }
            $globalDirectives = $defaultGlobalDirectives;
        }
        if(true !== $chatKey){
            return $globalDirectives[$chatKey] ?? '';
        }
        return $globalDirectives;
    }

}
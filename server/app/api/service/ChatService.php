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
namespace app\api\service;
use app\common\enum\{chat\Api2dEnum,
    chat\ChatEnum,
    chat\XingHuoEnum,
    ChatRecordEnum,
    PayEnum,
    user\AccountLogEnum,
    YesNoEnum};
use app\common\logic\{AccountLogLogic, BaseLogic};
use app\common\model\{
    user\User,
    ChatRecords,
    ChatCategory,
    user\UserMember,
    member\MemberPackage
};
use app\common\service\{chat\GeminiService,
    chat\MiniMaxService,
    ConfigService,
    chat\QwenService,
    chat\WenXinService,
    chat\AiChatService,
    chat\ChatGlmService,
    chat\ChatGptService,
    chat\HunYuanService,
    chat\SystemChatService,
    chat\XingHuoService,
    chat\AzureOpenAIService,
    FileService};
use think\{Exception, facade\Db, facade\Log};

/**
 * 对话服务类类
 */
class ChatService  extends BaseLogic
{

    protected $source           = '';           //模型对应公司
    protected $chatKey          = '';           //当前模型的key
    protected $chatModel        = '';           //对话模型
    protected $userId           = '';           //用户id
    protected $user             = '';           //用户信息
    protected $config           = [];           //chat全部配置
    protected $chatConfig       = [];           //chat配置
    protected $userMember       = false;        //用户是否为会员
    protected $type             = '';           //对话类型：1-简单对话；2-创作对话；3-技能对话
    protected $otherId          = '';           //创作id|技能id
    protected $categoryId       = '';           //会话id
    protected $question         = '';           //提问问题
    protected $form             = [];           //创作表单
    protected $ask              = '';           //用户提问问题
    protected $reply            = '';           //回复内容
    protected $modelContent     = '';           //创作、技能表数据
    protected $messages         = [];           //上下文内容
    protected $billingModel     = [];           //计费模型
    protected $billingKey       = '';           //计费模型key

    protected $chatService      = '';            //对话实例类

    protected $replyType        = 1;             //回复类型：1-模型回复；2-系统默认回复

    protected $network          = false;         //联网

    protected $voiceFile        = '';           //音频文件

    protected $networkData      = '';            //联网数据

    protected $prompt           = [];            //指令


    public function __construct(int $userId,$params = [])
    {
        //设置返回流数据
        header('Connection: keep-alive');
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('X-Accel-Buffering: no');

        //如果不是用户自己选择的模型
        $this->config       = ConfigService::get('chat_config');

        $defaultModel = $params['model'] ?? '';
        $this->billingKey = $params['model'] ?? '';
        if($defaultModel){
            $this->chatKey   = ChatEnum::getDefaultBillingConfig($defaultModel)['chat_key'];
            if(in_array($this->chatKey,[ChatEnum::API2D_35,ChatEnum::API2D_40])){
                $defaultModel = Api2dEnum::getAliasNameList($defaultModel,true);
            }
            $chatConfig         = $this->config[$this->chatKey] ?? [];
            $defaultConfig      = ChatEnum::getDefaultChatConfig($this->chatKey);
            $this->chatConfig   = array_merge($defaultConfig,$chatConfig);
            //替换成选中模型
            $this->chatConfig['model'] = $defaultModel;

        }else{
            $this->chatKey = ConfigService::get('chat_config','type', ChatEnum::ZHIPUGLM);
            $chatConfig         = $this->config[$this->chatKey] ?? [];
            $defaultConfig      = ChatEnum::getDefaultChatConfig($this->chatKey);
            $this->chatConfig   = array_merge($defaultConfig,$chatConfig);
            $this->billingKey = $this->chatConfig['model'];
            if(in_array($this->chatKey,[ChatEnum::API2D_35,ChatEnum::API2D_40])){
                $this->billingKey = Api2dEnum::getAliasNameList($this->chatConfig['model']);
            }
        }
        $this->source       = $this->chatConfig['source'];
        $this->chatModel    = $this->chatConfig['model'];

        $this->otherId      = $params['other_id'] ?? '';
        $this->categoryId   = $params['other_id'] ?? ($params['category_id'] ?? 0);
        $this->question     = $params['question'] ?? $params['form'];
        //设置问题
        $this->ask          = $this->question;
        $this->userId       = $userId;
        $this->type         = $params['type'] ?? '';
        $this->user         = User::where('id',$this->userId)->findOrEmpty();

        //用户是否为会员；
        $this->userMember = false;
        if($this->user->member_perpetual || ($this->user->member_end_time && $this->user->member_end_time > time())){
            $this->userMember = true;
        }
        //兼容旧版本参数
        if(ChatRecordEnum::CHAT_QUESTION == $this->type){
            $this->otherId = 0;
        }else{
            $this->categoryId = 0;
        }
        //是否开启默认回复
        $defaultReplyOpen = $this->config['default_reply_open'] ??  config('project.chat_config.default_reply_open');
        if($defaultReplyOpen){
            $this->config['default_reply'] = $this->config['default_reply'] ??  config('project.chat_config.default_reply');
            $this->source = '';
            $this->replyType = 2;
        }
        //目前只支持openai联网
        if('openai' == $this->source || 'azure' == $this->source){
            $this->network = $params['network'] ?? false;
        }
        if(isset($params['voice_file'])){
            $this->voiceFile = $params['voice_file'];
        }
    }


    /**
     * @notes 验证会员
     * @return bool
     * @throws Exception
     * @author cjhao
     * @date 2023/6/21 15:41
     */
    public function checkUser()
    {
        //验证用户
        if ($this->user->isEmpty()) {
            throw new Exception('非法会员');
        }
        if(YesNoEnum::YES == $this->user->is_blacklist){
            throw new Exception('您已被管理员禁止提问，请联系客服详询原因。');
        }
        $this->billingModel = AiChatService::getBillingModel($this->userId,$this->billingKey);
        if(empty($this->billingModel)){
            throw new Exception('当前模型不支持使用');
        }
        if($this->billingKey != $this->billingModel['key']){
            throw new Exception('当前模型不支持使用');
        }
        //如果是系统对话，直接返回
        if(ChatRecordEnum::REPLYTYPE_SYSTEM == $this->replyType){
            return true;
        }
        $consumeBalance = 0;
        //如果不是会员且不是默认模型、记录消耗次数
        if(false == $this->billingModel['member_free'] && $this->billingModel['balance'] > 0){
            $consumeBalance += $this->billingModel['balance'];
        }
        //使用联网插件额外扣费
        if($this->network && false == $this->userMember ){
            $networkBalance = $this->config['network_balance'] ?? 1;
            $consumeBalance += $networkBalance;
        }
        //用户当前余额不够，直接抛异常
        if($consumeBalance > $this->user->balance){
            throw new Exception('对话余额不足',101);
        }
        //如果不是会员或者不是会员默认模型直接返回
        if(false == $this->userMember || false == $this->billingModel['member_free']){
            return true;
        }
        //验证会员今天对话次数是否达到限制
        $userMemberInfo = UserMember::field('package_info')
            ->where(['user_id'=>$this->userId,'refund_status'=>PayEnum::REFUND_NOT])
            ->json(['package_info'],true)
            ->order(['sort'=>'desc','is_perpetual'=>'desc','add_member_time'=>'desc','id'=>'desc'])
            ->findOrEmpty()->toArray();
        if(empty($userMemberInfo)){
            return true;
        }
        $memberPackage = MemberPackage::where(['id'=>$userMemberInfo['package_info']['id'] ?? 0])
            ->findOrEmpty()
            ->toArray();
        if(empty($memberPackage) || empty($memberPackage['chat_limit'])){
            return true;
        }
//        //今天对话的次数
//        $todayChatNum = ChatRecords::where(['user_id'=>$this->userId])
//            ->whereDay('create_time')
//            ->count();
//        //对话超出次数，提示
//        if ($todayChatNum >= $memberPackage['chat_limit']) {
//            $chatLimitTips = ConfigService::get('chat_config','chat_limit_tips','今日对话次数已达上限');
//            throw new Exception($chatLimitTips);
//        }
    }

    /**
     * @notes 设置对话参数
     * @return void
     * @throws Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author caijianhao
     * @date 2023/8/30 10:58
     */
    public function setChatParams()
    {
        $temperature = $this->chatConfig['temperature'] ?? 0;
        if($temperature){
            $this->chatService->setTemperature($this->chatConfig['temperature']);
        }

        //加上全局指令
        $globalDirectives = AiChatService::getChatDirectives($this->chatKey);
        if($globalDirectives){
            $this->prompt[] = $globalDirectives;
        }

        if(in_array($this->type,[ChatRecordEnum::CHAT_CREATION,ChatRecordEnum::CHAT_SKILL])){
            //创作拼接问题
            if( $this->type == ChatRecordEnum::CHAT_CREATION){
                //如果有表单数据，直接用表单
                $this->ask = '';
                $this->form = $this->question;
                if($this->modelContent['form']){
                    //对话问题替换，方便保存到数据库
                    $this->question = $this->modelContent['content'];
                    foreach ($this->modelContent['form'] as $formKey => $formVal) {
                        $id = $formVal['id'];
                        $form = $this->form[$id] ?? '';
                        if($formVal['props']['isRequired'] && empty($form)){
                            throw new Exception('请输入：'.$formVal['props']['title'].'的内容');
                        }
                        if(is_array($form)){
                            $form = implode('、',$form);
                        }
                        $replaceStr = '${'.$id.'}';
                        $this->question = str_replace($replaceStr,$form,$this->question);
                        $this->ask .= $formVal['props']['title'].':'.$form.'；';
                    }
                }else{
                    //兼容旧版本数据
                    if(is_array($this->question)){
                        $this->question = reset($this->question);
                    }
                    $this->question = $this->modelContent['name'].':'.$this->question;
                    $this->ask = $this->question;
                }

                //创作不联系上下文
                $this->chatService->setContextNum(0);
            }

            $this->chatService->setTemperature($this->modelContent['temperature']);
            //然后是chatgpt加上更多参数
            if($this->chatService instanceof ChatGptService){
                $this->chatService->setN($this->modelContent['n'])
                    ->setTopP($this->modelContent['top_p'])
                    ->setPresencePenalty($this->modelContent['presence_penalty']);


            }
            if(ChatRecordEnum::CHAT_SKILL == $this->type){
                $this->prompt[] = $this->modelContent['content'];
            }
        }

        //设置指令
        foreach ($this->prompt as $prompt){
            switch (true) {
                case $this->chatService instanceof QwenService:
                case $this->chatService instanceof ChatGptService:
                case $this->chatService instanceof ChatGlmService:
                case $this->chatService instanceof XingHuoService:
                case $this->chatService instanceof AzureOpenAIService:
                    // 全局审核指令
                    $this->messages[] = ['role' => 'system', 'content' => $prompt];
                    break;
                case $this->chatService instanceof WenXinService:
                case $this->chatService instanceof MiniMaxService:
                    $this->chatService->setSystem($prompt);
                    break;
            }
        }

        //思维导图、不需要关联上下文、回复个数也是1
        if($this->chatService instanceof ChatGptService || $this->chatService instanceof AzureOpenAIService){
            if(ChatRecordEnum::CHAT_MIND  == $this->type || $this->voiceFile){
                $this->chatService->setN(1);
            }
        }
        //思维导图不需要关联上下文
        if(ChatRecordEnum::CHAT_MIND  == $this->type){
            $this->chatService->setContextNum(0);
        }


        //如果使用了联网插件，不需要关联上下文
        if($this->network){

            $this->networkData = AiChatService::getNetworkData($this->question);
            $chineseWeekDay = getChineseWeekDay();
            $date = '今天是:'.date('Y年m月d号').'，星期'.$chineseWeekDay.'，北京时间: '.date('H:i:s');
            $networkSystem = $this->config['network_system'] ?? config('project.chat_config.network_system');
            $networkData = str_replace('{networkData}', $this->networkData, $networkSystem);
            $networkData = str_replace('{date}', $date, $networkData);
            $this->messages[] = ['role' => 'system', 'content' => $networkData];
            $this->question = "我的问题是:".$this->question;


        }else{
            $contentNum = $this->chatService->getContextNum();
            //联系上下文
            if($contentNum > 0){
                //查找对话内容
                $where[] = ['user_id','=',$this->userId];
                $where[] = ['is_show','=',1];
                $where[] = ['type','=',$this->type];
                $where[] = ['other_id','=',$this->otherId];
                $where[] = ['category_id','=',$this->categoryId];
                $chatRecords = ChatRecords::where($where)
                    ->limit($contentNum)
                    ->order('id desc')
                    ->select()->toArray();
                $chatRecords = array_reverse($chatRecords);

                //有联系上下文，处理对话记录
                foreach ($chatRecords as $record){
                    $ask = $record['ask'];
                    if (is_array($ask)) {
                        $ask = implode('，',$ask);
                    }
                    $reply = $record['reply'];
                    if (is_array($reply)) {
                        $reply = implode('，',$reply);
                    }
                    if($ask && $reply){
                        $this->messages[] = ['role'  => 'user','content' => (string)$ask];
                        $this->messages[] = ['role'  => 'assistant','content' => (string)$reply];
                    }
                }
            }

        }

        //思维导图拼接问题
        if($this->type == ChatRecordEnum::CHAT_MIND){
            $this->messages = [];
            $cueWord = ConfigService::get('mindmap_config', 'cue_word',config('project.mindmap_config.cue_word'));
            $cueWord = str_replace('{prompt}', $this->question, $cueWord);
            $this->messages[] = ['role'  => 'user','content' =>  $cueWord];
        } else {
            $this->messages[] = ['role'  => 'user','content' =>  $this->question];
        }
    }

    /**
     * @notes 对话接口
     * @return bool
     * @throws \DfaFilter\Exceptions\PdsBusinessException
     * @throws \DfaFilter\Exceptions\PdsSystemException
     * @author cjhao
     * @date 2023/6/21 18:15
     */
    public function chat()
    {
        try{
            //验证参数
            $this->checkParams();
            //选取AI对象
            $this->chatService = match ($this->source) {
                'openai'         => (new ChatGptService($this->chatConfig)),
                'zhipu'          => (new ChatGlmService($this->chatConfig)),
                'xunfei'         => (new XingHuoService($this->chatConfig)),
                'baidu'          => (new WenXinService($this->chatConfig)),
                'qwen'           => (new QwenService($this->chatConfig)),
                'hunyuan'        => (new HunYuanService($this->chatConfig)),
                'azure'          => (new AzureOpenAIService($this->chatConfig)),
                'minimax'        => (new MiniMaxService($this->chatConfig)),
                'google'         => (new GeminiService($this->chatConfig)),
                default          => (new SystemChatService($this->config)),
            };
            //设置参数
            $this->setChatParams();
            //敏感词过滤
            AiChatService::Sensitive($this->question);
            //问题审核
            AiChatService::askCensor($this->question);
            //发起对话
            $this->chatService->chatStreamRequest($this->messages);
            //获取回复内容
            $this->reply        = $this->chatService->getReplyContent();
            //获取当前使用的模型
            $this->chatModel    = $this->chatService->getModel();
            Db::startTrans();
            //记录用户信息
            $this->recordsUserLog();
            Db::commit();
            return true;
        }catch (Exception $e){
            Log::write('对话报错：'.$e->getFile().'-'.$e->getLine().'-'.$e->getMessage());
            AiChatService::parseReturnErrorStream(
                'error',
                $e->getMessage(),
                $this->chatKey,
                $e->getCode()
            );
        }
    }

    /**
     * @notes 验证参数
     * @throws Exception
     * @author cjhao
     * @date 2023/6/21 15:49
     */
    public function checkParams()
    {
        if(!in_array($this->type,[ChatRecordEnum::CHAT_QUESTION,ChatRecordEnum::CHAT_CREATION,ChatRecordEnum::CHAT_SKILL,ChatRecordEnum::CHAT_MIND])){
            throw new Exception('type错误');
        }
        $this->checkOtherId();
        $this->checkUser();
    }

    /**
     * @notes 验证数据
     * @return bool|string
     * @author cjhao
     * @date 2023/6/21 15:43
     */
    public function checkOtherId()
    {
        if(empty($this->question)){
            throw new Exception('请输入内容');
        }
        if(empty($this->type)){
            throw new Exception('类型参数缺少');
        }
        //验证会话id
        if(ChatRecordEnum::CHAT_QUESTION == $this->type) {
            if(empty($this->categoryId)){
                throw new Exception('请选择会话');
            }

            $chatCategory = ChatCategory::where(['id' => $this->categoryId, 'user_id' => $this->userId])->findOrEmpty();
            if($chatCategory->isEmpty()){
                throw new Exception('会话不存在');
            }
        }
        if (in_array($this->type,[ChatRecordEnum::CHAT_CREATION,ChatRecordEnum::CHAT_SKILL])) {

            if(empty($this->otherId)){
                throw new Exception('请选择模型');
            }
            $model = ChatRecordEnum::getModel($this->type);
            $model = $model::where(['id' => $this->otherId])->findOrEmpty();
            if($model->isEmpty()){
                throw new Exception('模型不存在');
            }
            $this->modelContent = $model;
        }


    }



    /**
     * @notes 记录用户对话记录、扣减余额
     * @param $params
     * @author cjhao
     * @date 2023/6/21 11:01
     */
    public function recordsUserLog()
    {
        $data = [
            'user_id'           => $this->userId,
            'category_id'       => $this->categoryId,
            'ask'               => $this->ask,
            'reply'             => $this->reply,
            'type'              => $this->type,
            'other_id'          => $this->otherId,
            'extra'             => $this->form,
            'key'               => $this->chatKey,
            'model'             => $this->chatModel,
            'reply_type'        => $this->replyType,
        ];
        //使用联网
        if($this->network){
            $networkBalance = $this->config['network_balance'] ?? 1;
            $networkPlugin = [
                'balance'   => $networkBalance,
                'data'      => $this->networkData,
            ];
            $data['network_plugin'] = $networkPlugin;
        }
        //语音对话类型，保存语音文件
        if($this->voiceFile){
            $data['voice_plugin'] = [
                'voice_input'   => FileService::setFileUrl($this->voiceFile)
            ];
        }
        //创建对话记录
        (new ChatRecords())->save($data);

        if(ChatRecordEnum::REPLYTYPE_SYSTEM == $this->replyType){
            return true;
        }
        //扣除用户余额,不是会员，且消耗次数大于零
        $this->user->total_quiz = $this->user->total_quiz + 1;
        $this->user->save();
        if(true != $this->billingModel['member_free'] && $this->billingModel['balance'] > 0){
            $this->user->balance = $this->user->balance - $this->billingModel['balance'];
//            $this->user->total_quiz = $this->user->total_quiz + 1;
            $this->user->save();
            $changeType = AccountLogEnum::UM_DEC_CHAT;
            if(ChatRecordEnum::CHAT_MIND == $this->type){
                $changeType = AccountLogEnum::UM_DEC_MIND_CHAT;
            }
            // 记录账户流水
            AccountLogLogic::add(
                $this->userId,
                $changeType,
                AccountLogEnum::DEC,
                $this->billingModel['balance']
            );
        }
        //联网消耗次数
        $networkBalance = $this->config['network_balance'] ?? 1;
        if($this->network && false == $this->userMember && $networkBalance > 0){
            $this->user->balance = $this->user->balance - $networkBalance;
            $this->user->save();
            //消耗次数
            AccountLogLogic::add(
                $this->userId,
                AccountLogEnum::UM_DEC_NETWORK_CHAT,
                AccountLogEnum::DEC,
                $networkBalance
            );

        }
    }

    /**
     * @notes 获取对话模型
     * @return array
     * @author cjhao
     * @date 2023/7/19 11:48
     */
    public static function getModel(int $userId)
    {
        $isOpen = ConfigService::get('chat_config','billing_is_open');
        $billingModelList = [];
        if($isOpen){
            $billingModelList = AiChatService::getBillingModel($userId);
        }
        return array_values($billingModelList);
    }

}
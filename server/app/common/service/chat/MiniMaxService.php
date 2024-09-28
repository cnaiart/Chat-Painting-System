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
use app\common\cache\KeyPoolCache;
use app\common\enum\chat\MiniMaxEnum;
use think\Exception;
use think\facade\Log;

/**
 * minimax服务类
 * Class MiniMaxService
 * @package app\common\service\chat
 */
class MiniMaxService implements ChatInterface{
    protected $apiKey               = '';
    protected $groudId              = '';
    protected $chatKey              = '';
    protected $model                = '';

    protected $keyPoolCache         = '';
    protected $baseUrl              = 'https://api.minimax.chat/';
    protected $config               = [];
    protected $temperature          = MiniMaxEnum::DAFAULT_CONFIG['temperature'];            //词汇属性
    protected $topP                 = MiniMaxEnum::DAFAULT_CONFIG['top_p'];                  //话题属性

    protected $contextNum           = MiniMaxEnum::DAFAULT_CONFIG['context_num'];            //联系上下文

    protected $headers              = [];
    protected $content              = [];                                                    //返回内容
    protected $system               = '';
    protected $roleMeta             = [];

    protected $requestId            = '';
    public function __construct(array $config = [])
    {
        $this->config   = $config;
        $this->chatKey  = $this->config['key'];
        //模型
        $this->model    = $this->config['model'];
        //获取key
        $this->keyPoolCache = (new KeyPoolCache($this->chatKey));
        $keyConfig=  $this->keyPoolCache->getKey();
        if(empty($keyConfig)){
            throw new Exception('请在后台配置key');
        }
        $this->apiKey = $keyConfig['key'];
        $this->groudId = $keyConfig['appid'];
        //模型
        $this->model = $this->config['model'] ?? MiniMaxEnum::ABAB55;

        //联系上下文
        $this->contextNum       = $this->config['context_num'];
        //词汇属性
        $this->temperature      = $this->config['temperature'];
        //随机属性
        $this->topP             = $this->config['top_p'];

        $this->headers['Content-Type'] = 'application/json';
        $this->headers['Authorization'] = 'Bearer '.$this->apiKey;
    }


    /**
     * @notes 对话流请求
     * @param array $messages
     * @return ChatInterface
     * @throws Exception
     * @author cjhao
     * @date 2024/1/5 18:30
     */
    public function chatStreamRequest(array $messages): ChatInterface
    {
        ignore_user_abort(true);
        $this->baseUrl.='/v1/text/chatcompletion?GroupId='.$this->groudId;
        $messagesLists = [];
        foreach ($messages as $message){
            $messagesLists[] =    [
                'sender_type'   => 'user' == $message['role'] ? 'USER' : 'BOT',
                'text'          => $message['content'],
            ];
        }
        $data = [
            'model'                 => $this->model, //聊天模型
            'messages'              => $messagesLists,
            'stream'                => true,
            'temperature'           => (float)$this->temperature,
//            'top_p'                 => (float)$this->topP,
        ];
        if($this->system){
            $data['prompt'] = $this->system;
            $data['role_meta'] = $this->roleMeta;
        }

        $response = true;
        $callback = function ($ch, $data) use (&$response,&$total){
            $result = @json_decode($data);
            $dataLength = strlen($data);
            if(isset($result->base_resp)){
                (new KeyPoolCache($this->chatKey))->headerErrorKey($result->base_resp->status_msg,$this->baseUrl);
                $response = 'miniMax:'.$result->base_resp->status_msg;
                return 1;
            }
            $this->parseStreamData($data);
            //客户端没断开
            if(connection_aborted()){
                return 1;
            }
            return $dataLength;
        };
        $headers  = [
//            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: Bearer '.$this->apiKey
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT,300);//设置300秒超时
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, $callback);
        curl_exec($ch);
        curl_close($ch);
        if(true !== $response){
            throw new Exception($response);
        }
        return $this;

    }


    /**
     * @notes 解析Minimax流数据
     * @param $data
     * @author cjhao
     * @date 2023/5/6 11:54
     */
    public function parseStreamData($stream)
    {
        $stream = str_replace("data: ", "", $stream);
        $data = json_decode($stream,true);

        $this->requestId = $data['request_id'] ?? $this->requestId;
        foreach ($data['choices'] as $index => $choices){
            $streamContent = $choices['delta'] ?? '';
            $content = $this->content[$index] ?? '';
            if(!isset($choices['finish_reason'])){
                $chatEvent = 'chat';
                //给前端发送流数据
                AiChatService::parseReturnStream(
                    $chatEvent,
                    $this->requestId,
                    $streamContent,
                    $index,
                    $this->chatKey
                );
            }else{
                $chatEvent = 'finish';
                AiChatService::parseReturnStream(
                    $chatEvent,
                    $this->requestId,
                    $streamContent,
                    $index,
                    $this->chatKey
                );
            }
            $this->content[$index] = $content.$streamContent;

        }
    }

    /**
     * @notes 获取回复内容
     * @return string|array
     * @author cjhao
     * @date 2024/1/5 18:21
     */
    public function getReplyContent(): string|array
    {
       return $this->content;
    }

    /**
     * @notes 设置联系上下文
     * @param $contextNum
     * @return ChatInterface
     * @author cjhao
     * @date 2024/1/5 18:21
     */
    public function setContextNum($contextNum): ChatInterface
    {
       $this->contextNum = $contextNum;
       return $this;
    }

    /**
     * @notes 获取联系上下文
     * @return int
     * @author cjhao
     * @date 2024/1/5 18:20
     */
    public function getContextNum(): int
    {
        return $this->contextNum;
    }

    /**
     * @notes
     * @param $temperature
     * @return ChatInterface
     * @author cjhao
     * @date 2024/1/5 18:17
     */
    public function setTemperature($temperature): ChatInterface
    {
        $this->temperature = $temperature;
        return $this;
    }

    /**
     * @notes 获取对话模型
     * @return string
     * @author cjhao
     * @date 2024/1/5 18:17
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * @notes 设置指令
     * @param $system
     * @return void
     * @author cjhao
     * @date 2023/12/7 12:00
     */
    public function setSystem($system)
    {
        $this->system = $system;
        $this->roleMeta = [
            'user_name' => '我',
            'bot_name'  => $system
        ];

    }

}
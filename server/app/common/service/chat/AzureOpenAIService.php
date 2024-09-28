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
use think\Exception;
use think\facade\Log;

/**
 * AzureOpenAI服务类
 */
class AzureOpenAIService implements ChatInterface
{
    protected $baseUrl              = '';
    protected $apiKey               = '';
    protected $model                = '';
    protected $config               = [];
    protected $temperature          = '';
    protected $presencePenalty      = '';
    protected $frequencyPenalty     = '';
    protected $n                    = '';
    protected $content              = [];

    protected $topP                 = '';
    protected $contextNum           = '';
    protected $chatKey              = '';
    protected $tokenTag             = 'azure_access_token_';


    public function __construct(array $config = [])
    {
        $this->config   = $config;

        //模型
        $this->model            = $this->config['model'];
//        $this->model            = 'mtu35';
        //联系上下文
        $this->contextNum       = $this->config['context_num'];
        //词汇属性
        $this->temperature      = $this->config['temperature'];
        //随机属性
        $this->topP             = $this->config['top_p'];
        //话题属性
        $this->presencePenalty  = $this->config['presence_penalty'];
        //重复属性
        $this->frequencyPenalty = 0;
        //最大回复
        $this->n                = $this->config['n'] ?? 1;


        $this->baseUrl = $this->config['agency_api'];

        $this->chatKey = $this->config['key'];

        //获取key
        $keyConfig = (new KeyPoolCache($this->chatKey))->getKey();
        if(empty($keyConfig)){
            throw new Exception('请在后台配置相关key');
        }
        $this->apiKey = $keyConfig;
    }

    /**
     * @notes chat请求流
     * @param array $messages
     * @return $this
     * @throws Exception
     * @author ljj
     * @date 2023/12/22 3:30 下午
     */
    public function chatStreamRequest(array $messages):self
    {
        ignore_user_abort(true);
        $url = $this->baseUrl.'openai/deployments/'.$this->model.'/chat/completions?api-version=2023-05-15';
        $data = [
            'messages' => $messages,
            'stream' => true,
            'temperature' => (float)$this->temperature,
            'top_p' => (float)$this->topP,
            'presence_penalty' => (float)$this->presencePenalty,
            'frequency_penalty' => (float)$this->frequencyPenalty,
            'n' => (int)$this->n,
        ];
        $header  = [
            'Content-Type: application/json',
            'api-key: '.$this->apiKey
        ];

        $response = true;
        $callback = function ($ch, $data) use (&$content,&$response,&$total){
            $result = @json_decode($data);
            if (isset($result->error)) {
                $response = $result->error->message;
                //处理报错key
                (new KeyPoolCache($this->chatKey))->headerErrorKey($response,$this->baseUrl);
            }else{
                $this->parseStreamData($data);
            }
            //客户端没断开
            if(connection_aborted()){
                return 1;
            }

            return strlen($data);
        };
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_TIMEOUT,100);//设置100秒超时
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, $callback);
        curl_exec($ch);
        curl_close($ch);

        if(true !== $response){
            throw new Exception('AzureOpenAI:'.$response);
        }
        return $this;
    }

    /**
     * @notes 解析AzureOpenAI流数据
     * @param $stream
     * @author ljj
     * @date 2023/12/22 10:55 上午
     */
    public function parseStreamData($stream)
    {
        $dataLists = explode("\n", $stream);
        $chatEvent = 'chat';
        foreach ($dataLists as $data){
            if(false === strpos($data,'data:')){
                continue;
            }
            if(false !== strpos($data,'data: [DONE]')){
                continue;
            }
            $data = str_replace("data: ", "", $data);

            $data = json_decode($data, true);
            //解析到数据是空的、可能是数据丢失问题
            if (empty($data) || !is_array($data)) {
                continue;
            }
            $index = $data['choices'][0]['index'] ?? 0;
            $finishReason = $data['choices'][0]['finish_reason'] ?? '';
            $streamContent = $data['choices'][0]['delta']['content'] ?? '';
            $id = $data['id'] ?? '';
            //结束标识
            if('stop' == $finishReason){
                $chatEvent = 'finish';

            }else{
                if (!isset($data['choices'][0]['delta']['content'])) {
                    Log::write("数据可能丢失:".$stream);
                    continue;
                }
                $contents = $this->content[$index] ?? '';
                $this->content[$index] = $contents.$streamContent;
            }
            //给前端发送流数据
            AiChatService::parseReturnStream(
                $chatEvent,
                $id,
                $streamContent,
                $index,
                $this->chatKey
            );
        }
    }

    /**
     * @notes 获取内容
     * @return array
     * @author ljj
     * @date 2023/12/21 11:50 上午
     */
    public function getReplyContent():array
    {
        return $this->content;
    }

    /**
     * @notes 获取上下文
     * @return int
     * @author ljj
     * @date 2023/12/21 11:51 上午
     */
    public function getContextNum():int
    {
        return $this->contextNum;
    }

    /**
     * @notes 设置上下文
     * @param $contextNum
     * @return $this
     * @author ljj
     * @date 2023/12/21 11:52 上午
     */
    public function setContextNum($contextNum):self
    {
        $this->contextNum = (int)$contextNum;
        return $this;
    }

    /**
     * @notes 获取对话模型
     * @return string
     * @author ljj
     * @date 2023/12/21 11:56 上午
     */
    public function getModel():string
    {
        return $this->model;
    }

    /**
     * @notes 词汇属性
     * @param $temperature
     * @author cjhao
     * @date 2023/6/13 17:31
     */
    public function setTemperature($temperature):self
    {
        $this->temperature = (float)$temperature;
        return $this;

    }

    /**
     * @notes 随机属性
     * @param $temperature
     * @author cjhao
     * @date 2023/6/13 17:31
     */
    public function setTopP($topP)
    {
        $this->topP = (float)$topP;
        return $this;

    }

    /**
     * @notes 话题属性
     * @param $temperature
     * @author cjhao
     * @date 2023/6/13 17:31
     */
    public function setPresencePenalty($presencePenalty)
    {
        $this->presencePenalty = (float)$presencePenalty;
        return $this;

    }

    /**
     * @notes 重复属性
     * @param $temperature
     * @author cjhao
     * @date 2023/6/13 17:31
     */
    public function setFrequencyPenalty($frequencyPenalty)
    {
        $this->frequencyPenalty = (float)$frequencyPenalty;
        return $this;

    }

    /**
     * @notes 最大回复
     * @param $n
     * @author cjhao
     * @date 2023/6/13 17:31
     */
    public function setN($n)
    {
        $this->n = intval($n ?:1);
        return $this;
    }
}
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
use app\common\{
    enum\DrawEnum,
    cache\KeyPoolCache,
    enum\chat\ChatEnum,
    enum\chat\OpenAiEnum,
    service\ConfigService,
};
use think\Exception;
use think\facade\Log;
use WpOrg\Requests\Requests;

/**
 * chatGpt服务类
 * Class ChatGtpService
 * @package app\common\service\chatgtp
 */
class ChatGptService  implements ChatInterface
{
    protected $apiKey               = '';
    protected $chatKey              = '';
    protected $model                = '';

    protected $keyPoolCache         = '';
    protected $baseUrl              = 'https://api.openai.com';
    protected $config               = [];
    protected $temperature          = OpenAiEnum::DAFAULT_CONFIG['temperature'];            //词汇属性
    protected $contextNum           = OpenAiEnum::DAFAULT_CONFIG['context_num'];            //联系上下文
    protected $presencePenalty      = OpenAiEnum::DAFAULT_CONFIG['presence_penalty'];       //话题属性
    protected $frequencyPenalty     = OpenAiEnum::DAFAULT_CONFIG['frequency_penalty'];      //重复属性
    protected $n                    = OpenAiEnum::DAFAULT_CONFIG['n'];                      //最大回复
    protected $topP                 = OpenAiEnum::DAFAULT_CONFIG['top_p'];                  //话题属性

    protected $headers = [];
    protected $content = [];                                                                //返回内容





    public function __construct(array $config = [])
    {
        if($config){
            $this->config   = $config;
        }else{
            //翻译：默认使用模型
            $defaulModel        = ConfigService::get('draw_config', 'translate', DrawEnum::getTranslateConfig())['model'] ?? ChatEnum::OPEN_GPT_35;
            $chatConfig         = ConfigService::get('chat_config',$defaulModel,[]);
            $defaultConfig      = ChatEnum::getDefaultChatConfig($defaulModel);
            $this->config       = array_merge($defaultConfig,$chatConfig);
        }
        
        $this->chatKey  = $this->config['key'];
        //模型
        $this->model = $this->config['model'];
        //替换成api2d的域名
        if(in_array($this->chatKey,[ChatEnum::API2D_35,ChatEnum::API2D_40])){
            $this->baseUrl = 'https://openai.api2d.net';
        }
        //获取key
        $this->keyPoolCache = (new KeyPoolCache($this->chatKey));
        $this->apiKey =  $this->keyPoolCache->getKey();
        if(empty($this->apiKey)){
            throw new Exception('请在后台配置key');
        }
        //模型
        $this->model = $this->config['model'] ?? OpenAiEnum::GPT35_TURBO;

        //联系上下文
        $this->contextNum       = $this->config['context_num'];
        //词汇属性
        $this->temperature      = $this->config['temperature'];
        //随机属性
        $this->topP             = $this->config['top_p'];
        //话题属性
        $this->presencePenalty  = $this->config['presence_penalty'];
        //重复属性
//        $this->frequencyPenalty= $this->config['frequency_penalty'];
        //最大回复
        $this->n                = $this->config['n'] ?? 1;
        //代理域名
        if($this->config['agency_api'] ?? ''){
            $this->baseUrl = $this->config['agency_api'];
        }
        $this->headers['Content-Type'] = 'application/json';
        $this->headers['Authorization'] = 'Bearer '.$this->apiKey;
    }

    /**
     * @notes 翻译
     * @param string $content
     * @return string
     * @throws Exception
     * @author cjhao
     * @date 2023/7/7 14:31
     */
    public function translate(string $prompt)
    {

        $this->baseUrl.='/v1/chat/completions';
        $data = [
            'model' => $this->model, //聊天模型
            'messages' => [
                ['role' => 'system', 'content' =>'你现在担任我的翻译助理，我会用任何语言和你交流，你只需将我的话翻译为英语，我会将翻译的结果用于如stable diffusion，midjournev等生成图片，所以请确保意思不变，但更适合此类场景。请按我接下来的要求返回内容'],
                ['role' => 'user',   'content' => $prompt]
            ],
        ];

        $options = [];
        //设置超时时间
        $options['timeout'] = 20;
        //不校验证书
        $options['verify'] = false;
        $response = Requests::post($this->baseUrl, $this->headers,json_encode($data),$options);
        $responseData = $this->getResponseData($response);
        $content = $responseData['choices'][0]['message']['content'] ?? '';
        if(empty($content)){
            throw new Exception('获取数据失败');
        }
        return $content;

    }

    /**
     * @notes 发起对话
     * @param array $params
     * @throws Exception
     * @author cjhao
     * @date 2023/4/24 11:42
     */
    public function chat(array $params)
    {

        $this->baseUrl.='/v1/chat/completions';
        $data = [
            'model' => $this->model, //聊天模型
            'messages' => $params['messages'],
        ];
        //设置超时时间
        $options['timeout'] = 100;
        $response = Requests::post($this->baseUrl, $this->headers,json_encode($data),$options);
        $responseData = $this->getResponseData($response);
        return $responseData;

    }


    /**
     * @notes chat请求流
     * @param array $messages
     * @throws Exception
     * @author cjhao
     * @date 2023/4/24 11:42
     */
    public function chatStreamRequest(array $messages):self
    {
        ignore_user_abort(true);
        $this->baseUrl.='/v1/chat/completions';
        $data = [
            'model'                 => $this->model, //聊天模型
            'messages'              => $messages,
            'stream'                => true,
            'temperature'           => (float)$this->temperature,
            'top_p'                 => (float)$this->topP,
            'presence_penalty'      => (float)$this->presencePenalty,
            'frequency_penalty'     => 0,
            'n'                     => (int)$this->n,
        ];
        $response = true;
        $section = '';
        $callback = function ($ch, $data) use (&$response,&$total,&$section){
            $result = @json_decode($data);
            $dataLength = strlen($data);
            if (isset($result->error) || (isset($result->object) && 'error' == $result->object)) {
                //api2d的报错
                if((isset($result->object) && 'error' == $result->object)){
                    $response = 'api2d:'.$result->message;
                    //处理报错key
                    $this->keyPoolCache->headerErrorKey($result->message,$this->baseUrl);
                    return 1;
                }
                //openai的报错
                if(isset($result->error)){
                    $response = 'openai:'.$result->error->message ? $result->error->message : $result->error->type;
                    //处理报错key
//                    $this->headerErrorKey($result);
                    $this->keyPoolCache->headerErrorKey($result->error->message ?? '',$this->baseUrl);
                    return 1;
                }

            }else{
                // 一条data可能会被截断分两次返回
                if (!empty($section)) {

                    $data = $section . $data;
                    $section = '';

                } else {

                    if (substr($data, -1) !== "\n") {
                        $section = $data;
                        return $dataLength;
                    }

                }

                $this->parseStreamData($data);
            }
            //客户端断开
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
     * @notes 获取请求结果
     * @param $response
     * @return array
     * @throws \Exception
     * @author cjhao
     * @date 2023/4/23 16:12
     */
    public function getResponseData($response):array
    {
        $responseData = json_decode($response->body,true);
        if(isset($responseData['error'])){
            throw new Exception($responseData['error']['message']);
        }
        return $responseData;

    }

    /**
     * @notes 解析openai流数据
     * @param $data
     * @return array
     * @author cjhao
     * @date 2023/5/6 11:54
     */
    public function parseStreamData($stream)
    {
        $dataLists = explode("\n\n", $stream);
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
     * @notes 获取联系上下文数量
     * @return int|mixed
     * @author cjhao
     * @date 2023/5/8 18:49
     */
    public function getContextNum():int
    {
        return $this->contextNum;

    }

    /**
     * @notes 设置上下文数量
     * @param $contextNum
     * @author cjhao
     * @date 2023/6/21 16:44
     */
    public function setContextNum($contextNum):self
    {
        $this->contextNum = (int)$contextNum;
        return $this;

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
    public function setTopP($topP):self
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
    public function setPresencePenalty($presencePenalty):self
    {
        $this->presencePenalty = (float)$presencePenalty;
        return $this;

    }


    /**
     * @notes 获取回复内容
     * @return array
     * @author cjhao
     * @date 2023/7/3 16:10
     */
    public function getReplyContent():array
    {
        return $this->content;
    }

    /**
     * @notes 获取对话模型
     * @return mixed|string
     * @author cjhao
     * @date 2023/7/25 11:11
     */
    public function getModel():string
    {
        return $this->model;

    }


    /**
     * @notes 重复属性
     * @param $temperature
     * @author cjhao
     * @date 2023/6/13 17:31
     */
    public function setFrequencyPenalty($frequencyPenalty):self
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
    public function setN($n):self
    {
        $this->n = intval($n ?:1);
        return $this;
    }

}
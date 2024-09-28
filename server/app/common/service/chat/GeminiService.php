<?php
// +----------------------------------------------------------------------
// | likeshop开源商城系统
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | gitee下载：https://gitee.com/likeshop_gitee
// | github下载：https://github.com/likeshop-github
// | 访问官网：https://www.likeshop.cn
// | 访问社区：https://home.likeshop.cn
// | 访问手册：http://doc.likeshop.cn
// | 微信公众号：likeshop技术社区
// | likeshop系列产品在gitee、github等公开渠道开源版本可免费商用，未经许可不能去除前后端官方版权标识
// |  likeshop系列产品收费版本务必购买商业授权，购买去版权授权后，方可去除前后端官方版权标识
// | 禁止对系统程序代码以任何目的，任何形式的再发布
// | likeshop团队版权所有并拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeshop.cn.team
// +----------------------------------------------------------------------

namespace app\common\service\chat;
use app\common\cache\KeyPoolCache;
use think\Exception;

/**
 * 腾讯混元服务类
 */
class GeminiService implements ChatInterface
{
    protected $baseUrl              = 'https://generativelanguage.googleapis.com';
    protected $appid                = '';
    protected $secretId             = '';
    protected $secretKey            = '';
    protected $model                = '';
    protected $config               = [];
    protected $topP                 = '';
    protected $temperature          = '';
    protected $contextNum           = '';
    protected $chatKey              = '';
    protected $apiKey               = '';
    protected $content = [];        //返回内容
    protected $section = '';




    public function __construct(array $config = [])
    {
        $this->config   = $config;
        //模型
        $this->model = $this->config['model'];
        $this->topP = $this->config['top_p'];
        $this->temperature = $this->config['temperature'];
        //联系上下文
        $this->contextNum = $this->config['context_num'];

        $this->chatKey = $this->config['key'];

        //获取key
        $this->apiKey = (new KeyPoolCache($this->chatKey))->getKey();
        if(empty($this->apiKey)){
            throw new Exception('请在后台配置相关key');
        }
        if($this->config['agency_api'] ?? ''){
            $this->baseUrl = $this->config['agency_api'];
        }
        $this->baseUrl .= '/v1/models/gemini-pro:streamGenerateContent?alt=sse';
    }


    /**
     * @notes chat请求流
     * @param array $messages
     * @return $this
     * @throws Exception
     * @author ljj
     * @date 2023/12/22 10:55 上午
     */
    public function chatStreamRequest(array $messages):self
    {
        ignore_user_abort(true);
        $messageLists = [];
        //数据特殊处理
        foreach ($messages as $message){
            $messageLists[] = [
                'role'      => 'user' == $message['role'] ? 'user': 'model',
                'parts'     =>  [
                    ['text'  => $message['content']]
                ]
            ];
        }
        $data = [
            'contents'          => $messageLists,
            'generationConfig'  => [
                'temperature'   => $this->temperature,
//                'topP'          => $this->topP,
            ]
        ];
        $header = [
            'Content-Type: application/json',
            'x-goog-api-client: genai-js/0.1.3',
            'x-goog-api-key: '.$this->apiKey,
        ];
        $response = true;
        $callback = function ($ch, $data) use (&$response){
            $result = @json_decode($data);

            if (isset($result->errot)) {
                $response = $result->error->message;
                //处理报错key
                (new KeyPoolCache($this->chatKey))->headerErrorKey($response,$this->baseUrl);
            }else{
                $this->parseStreamData($data);
            }
            //客户端断开
            if(connection_aborted()){
                return 1;
            }
            return strlen($data);
        };
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
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
            throw new Exception('Gemini:'.$response);
        }
        //gemini没有结束标识，到这里给用户推送结束流
        AiChatService::parseReturnStream(
            'finish',
            0,
            '',
            0,
            $this->chatKey
        );
        return $this;
    }

    /**
     * @notes 解析流数据
     * @param $stream
     * @author ljj
     * @date 2023/12/22 10:55 上午
     */
    public function parseStreamData($stream)
    {
        $contents = $this->content[0] ?? '';
        $chatEvent = 'chat';
        $id = 0;
        $index = 0;
        //如果包含data:开头
        if(false !== strpos($stream,'data:')){
            $streamData = str_replace("data: ", "", $stream);
            $data = json_decode($streamData, true);
            if(empty($data)){
                $this->section = $streamData;
            }else{
                $streamContent = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
            }
        }else{
            //如果不是data开头，拼接上个数据
            $this->section.= $stream;
            $data = json_decode($this->section, true);
            //数据不完整，继续接收接下来的数据，拼接数据
            if(empty($data)){
                return ;
            }
            $streamContent = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
        }
        $this->content[0] = $contents.$streamContent;
        //给前端发送流数据
        AiChatService::parseReturnStream(
            $chatEvent,
            $id,
            $streamContent,
            $index,
            $this->chatKey
        );

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


    public function setTopP($topP):self
    {
        $this->topP = (float)$topP;
        return $this;
    }


    public function setTemperature($temperature):self
    {
        $this->temperature = (float)$temperature;
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
}
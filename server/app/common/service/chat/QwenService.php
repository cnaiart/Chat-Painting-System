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
 * 通义千问服务类
 */
class QwenService implements ChatInterface
{
    protected $baseUrl              = 'https://dashscope.aliyuncs.com/api/v1/services/aigc/text-generation/generation';
    protected $apiKey               = '';
    protected $model                = '';
    protected $config               = [];
    protected $seed                 = 1234;//生成时，随机数的种子，用于控制模型生成的随机性。如果使用相同的种子，每次运行生成的结果都将相同；支持无符号64位整数类型
    protected $topP                 = '';
    protected $topK                 = '';
    protected $repetitionPenalty    = '';
    protected $temperature          = '';
    protected $incrementalOutput    = True;//控制流式输出模式，默认False，即后面内容会包含已经输出的内容；设置为True，将开启增量输出模式，后面输出不会包含已经输出的内容
    protected $contextNum           = '';      //联系上下文
    protected $chatKey              = '';
    protected $tokenTag             = 'qwen_access_token_';


    public function __construct(array $config = [])
    {
        $this->config   = $config;

        //模型
        $this->model = $this->config['model'];
        $this->topP = $this->config['top_p'];
        $this->topK = $this->config['top_k'];
        $this->repetitionPenalty = $this->config['repetition_penalty'];
        $this->temperature = $this->config['temperature'];
        //联系上下文
        $this->contextNum = $this->config['context_num'];

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
     * @date 2023/12/21 11:48 上午
     */
    public function chatStreamRequest(array $messages):self
    {
        ignore_user_abort(true);
        $header = [
            'Authorization: Bearer '.$this->apiKey,
            'Content-Type: application/json',
            'X-DashScope-SSE: enable',
        ];
        $data = [
            'model' => $this->model,
            'input' => [
                'messages' => $messages,
            ],
            'parameters' => [
//                'top_p' => $this->topP,
//                'top_k' => $this->topK,
//                'repetition_penalty' => $this->repetitionPenalty,
                'temperature' => $this->temperature,
                'incremental_output' => $this->incrementalOutput,
            ],
        ];
        $response = true;
        $callback = function ($ch, $data) use (&$content,&$response,&$total){
            $result = @json_decode($data);
            if (!$result) {
                $dataArr = explode("\n", $data);
                foreach ($dataArr as $val){
                    if(false === strpos($val,'data:')){
                        continue;
                    }
                    $error = str_replace("data:", "", $val);
                    $error = json_decode($error, true);
                    if (isset($error['code'])) {
                        throw new Exception('通义千问:'.$error['message'] ?? $error['code']);
                    }
                }
            }
            if (isset($result->code)) {
                $response = $result->message;
                //处理报错key
                (new KeyPoolCache($this->chatKey))->headerErrorKey($result->message,$this->baseUrl);
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
            throw new Exception('通义千问:'.$response);
        }
        return $this;
    }

    /**
     * @notes 解析通义千问流数据
     * @param $stream
     * @author ljj
     * @date 2023/12/21 11:47 上午
     */
    public function parseStreamData($stream)
    {
        $dataLists = explode("\n", $stream);
        $chatEvent = 'chat';
        foreach ($dataLists as $data){
            if(false === strpos($data,'data:')){
                continue;
            }
            $data = str_replace("data:", "", $data);

            $data = json_decode($data, true);
            //解析到数据是空的、可能是数据丢失问题
            if (empty($data) || !is_array($data)) {
                continue;
            }

            $index = 0;
            $finishReason = false;
            $id = str_replace("id:", "", $dataLists[0] ?? '');
            $streamContent = $data['output']['text'] ?? '';
            $isEnd = $data['output']['finish_reason'] ?? '';
            if('stop' === $isEnd || 'length' === $isEnd){
                $finishReason = true;
            }
            //结束标识
            if($finishReason){
                $chatEvent = 'finish';
            }
            $contents = $this->content[$index] ?? '';
            $this->content[$index] = $contents.$streamContent;
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


    public function setTopP($topP):self
    {
        $this->topP = (float)$topP;
        return $this;
    }


    public function setTopK($topK):self
    {
        $this->topK = (float)$topK;
        return $this;
    }


    public function setTemperature($temperature):self
    {
        $this->temperature = (float)$temperature;
        return $this;
    }


    public function setRepetitionPenalty($repetitionPenalty):self
    {
        $this->repetitionPenalty = (float)$repetitionPenalty;
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
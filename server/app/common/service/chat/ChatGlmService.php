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
use app\common\enum\chat\ChatEnum;
use app\common\enum\chat\WenXinEnum;
use app\common\enum\chat\ZhiPuEnum;
use app\common\service\zhipuai\智谱apikey格式：;
use app\common\service\zhipuai\过期时间;
use Firebase\JWT\JWT;
use think\event\LogWrite;
use think\Exception;
use think\facade\Log;

/**
 * 智谱chat服务类
 * Class ZhiPuChatService
 * @package app\common\service\zhipuai
 */
class ChatGlmService implements ChatInterface
{
    protected $apiKey               = '';
    protected $chatKey              = '';
    protected $model                = '';

    protected $token                = '';
    protected $baseUrl              = 'https://open.bigmodel.cn';
    protected $config               = [];
    protected $temperature          = ZhiPuEnum::DAFAULT_CONFIG['temperature'];       //词汇属性
    protected $contextNum           = ZhiPuEnum::DAFAULT_CONFIG['context_num'];       //联系上下文
    protected $topP                 = ZhiPuEnum::DAFAULT_CONFIG['top_p'];             //话题属性
    protected $content              = [];                                             //返回数据
    protected $usage                = [];                                             //返回token使用量
    protected $section              = '';                                             //用于处理流数据不完整问题

    public function __construct(array $config)
    {
        //默认数据
        $this->config = $config;
        //模型
        $this->model            = $config['model'];
        $this->chatKey          = $config['key'];
        //联系上下文
        $this->contextNum       = $this->config['context_num'];
        //词汇属性
        $this->temperature      = $this->config['temperature'];
        //随机属性
        $this->topP             = $this->config['top_p'];
        $this->apiKey = (new KeyPoolCache($this->chatKey))->getKey();
        if(empty($this->apiKey)){
            throw new Exception('请在后台配置api_key');
        }
        //通过jwt包生成令牌
        $this->token = $this->generateToken($this->apiKey, 86400 * 7);

        $this->baseUrl .='/api/paas/v4/chat/completions';
        //请求地址
//        $this->baseUrl = match ($this->model) {
//            ZhiPuEnum::CHATGLM_TURBO        => $this->baseUrl.='/api/paas/v3/model-api/chatglm_turbo/sse-invoke',
//            ZhiPuEnum::CHATGLM_4            => $this->baseUrl.='/api/paas/v4/chat/completions',
//        };


    }

    /**
     * @notes jwt生成token
     * @param $apiKey   智谱apikey格式：{id}.{secret}
     * @param $expireTime 过期时间
     * @return string
     * @throws Exception
     * @author cjhao
     * @date 2023/6/19 17:37
     */
    private function generateToken(string $apiKey,int $expireTime){
        [$id, $secret] = explode('.', $apiKey);
        // 构造token的payload部分
        $nowMs = round(microtime(true) * 1000);

        // Claim构成 {"api_key":"id","exp":1617847620516,"timestamp":1617847616516}
        $payload = [
            'api_key'   => $id,
            'exp'       => ($nowMs + ($expireTime * 1000)),
            'timestamp' => $nowMs
        ];
        return JWT::encode($payload, $secret, 'HS256',null,['sign_type'=> 'SIGN']);
    }
    /**
     * @notes 智谱ai请求流
     * @return array $messages
     * @throws \think\Exception
     * @author cjhao
     * @date 2023/6/19 17:22
     */
    public function chatStreamRequest(array $messages):self
    {

        ignore_user_abort(true);
        $data = [
            'model'                 => ZhiPuEnum::getModelCode($this->model),
            'messages'              => $messages,
            'temperature'           => (float)$this->temperature,
            'stream'                => true,
        ];
        $response = true;
        $callback = function ($ch, $data) use (&$content,&$response,&$total){
            $result = @json_decode($data);
            if (isset($result->error->code)) {
                $response = '智谱ai:'.$result->error->message;
                //处理报错key
                (new KeyPoolCache($this->chatKey))->headerErrorKey($result->error->message,$this->baseUrl);
            }else{
                Log::write($data);
                $this->newParseStreamData($data);
            }
            //客户端没断开
            if(connection_aborted()){
                return 1;
            }
            return strlen($data);
        };
        $headers  = [
            'Content-Type: application/json',
            'Authorization: '.$this->token
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT,100);//设置100秒超时
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
     * @notes 解析流数据
     * @param $stream
     * @author cjhao
     * @date 2024/1/24 11:47
     */
    public function newParseStreamData($stream)
    {
        $id = 0;
        $index = 0;
        $chatEvent = 'chat';
        $streamLists = explode("\n\n", $stream);
        foreach ($streamLists as $key => $streamData){

            $data = str_replace("data: ", "", $streamData);
            $data = json_decode($data, true);
            //解析出来的是数据不完整情况，
            if(empty($data)){
                //则按拼接上一个不完整数据，如果是第一个不完整数据，则先保存起来
                if(empty($this->section)){
                    $this->section = $streamData;
                    continue;
                }else{
                    $this->section.= $streamData;
                    $data = str_replace("data: ", "", $this->section);
                    $data = json_decode($data, true);
                    //数据不完整，继续接收接下来的数据，拼接数据
                    if(empty($data)){
                        continue;
                    }
                    $this->section = '';
                }
            }
            //处理数据
            $streamContent = $data['choices'][0]['delta']['content'];
            $finishReason = $data['choices'][0]['finish_reason'] ?? '';
            if('stop' == $finishReason){
                $chatEvent = 'finish';
            }
            $id = $data['id'];
            $contents = $this->content[0] ?? '';
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

    }
    /**
     * @notes 解析流数据
     * @param $stream
     * @author cjhao
     * @date 2023/6/19 18:34
     * @deprecated
     */
    public function parseStreamData($stream)
    {
        $streamLists = explode("\n", $stream);
        //解析每一行并存储在关联数组中
        $streamData = [];
        $count = count($streamLists)-2;
        array_splice($streamLists,$count,2);
        foreach ($streamLists as $item => $data) {
            //按冒号拆分出属性名称与值
            $dataLists = explode(':', $data);
            //数据解析错误
            if(2 != count($dataLists)){
                continue;
            }
            $attr = $dataLists[0] ?? '';
            $value = $dataLists[1] ?? '';
            if('' === $attr){
                continue;
            }
            //每段流data数据可能是多个
            if('data' === $attr ){
                //如果只返回一个data,且data是空，数据丢掉
                if($count < 5 && '' === $value){
                    $streamData[$attr] = $value;
                    continue;
                }
                $data = $streamData[$attr] ?? '';
                //如果数组长度大于5个，解析里面的换行符号：1.内容直接空，补上换行符号；2.内容是   ，补上换行符号
                if('' === $value){
                    $streamData[$attr] = $data."\n";
                }elseif('   ' === $value && isset($streamLists[$item+1]) && !empty($streamLists[$item+1])){
                    $streamData[$attr] = $data."\n";
                }else{
                    $streamData[$attr] = $data.$value;
                }

            }else{
                $streamData[$attr] = $value;
            }
        }
        $event = $streamData['event'] ?? '';
        $streamContent = $streamData['data'] ?? '';
        if('' === $event ){
            return ;
        }
        $id = $streamData['id'] ?? '';
        $chatEvent = 'chat';
        switch ($event){
            case 'add':
            case 'error':
            case 'interrupted':
                $this->content .= $streamContent;
                break;
            case 'finish':
                $this->usage = $streamData['meta'] ?? [];
                $chatEvent = 'finish';
                break;
        }
        AiChatService::parseReturnStream($chatEvent,$id,$streamContent,0,$this->chatKey);
    }

    /**
     * @notes 获取内容
     * @return array
     * @author cjhao
     * @date 2023/6/19 19:06
     */
    public function getReplyContent():array
    {
        return $this->content;
    }

    /**
     * @notes 获取数量统计信息
     * @return array
     * @author cjhao
     * @date 2023/6/19 19:06
     */
    public function getUsage()
    {
        return $this->usage;
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
    public function setTopP($topP)
    {
        $this->topP = $topP;
        return $this;
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
}

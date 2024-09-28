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
use app\common\enum\chat\ChatEnum;
use app\common\enum\chat\WenXinEnum;
use app\common\service\ConfigService;
use think\Exception;
use think\facade\Cache;
use WpOrg\Requests\Requests;

/**
 * 百度文心一言服务类
 * Class BaiduService
 * @package app\common\service\baiduai
 */
class WenXinService implements ChatInterface
{
    protected $tokenTag             = 'yiyan_access_token_';
    protected $tokenCacheTag        = '';
    protected $apiKey               = '';
    protected $secretKey            = '';
    protected $accessToken          = '';
    protected $baseUrl              = 'https://aip.baidubce.com';
    protected $model                = 'ERNIE-Bot';
    protected $config               = [];
    protected $chatKey              = '';
    protected $temperature          = WenXinEnum::DAFAULT_CONFIG['temperature'];      //词汇属性
    protected $contextNum           = WenXinEnum::DAFAULT_CONFIG['context_num'];      //联系上下文
    protected $content              = [];                                            //返回内容
    protected $usage                = [];                                            //返回token使用量
    protected $header               = [];                                            //头信息
    protected $system               = '';                                            //文心一言指令


    public function __construct(array $config = [])
    {
        if($config){
            //默认数据
            $this->config   = $config;

        }else{
            //翻译：默认使用模型
            $chatConfig = ConfigService::get('chat_config',ChatEnum::WENXIN,[]);
            $defaultConfig      = ChatEnum::getDefaultChatConfig(ChatEnum::WENXIN);
            $this->config   = array_merge($defaultConfig,$chatConfig);
        }
        //词汇属性
        $this->temperature = $this->config['temperature'];
        //联系上下文
        $this->contextNum = $this->config['context_num'];
        //模型
        $this->model = $this->config['model'];
        $this->chatKey = $this->config['key'];
        //请求地址
        $this->baseUrl = match ($this->model) {
             WenXinEnum::ERNIE_BOT        => $this->baseUrl.'/rpc/2.0/ai_custom/v1/wenxinworkshop/chat/completions',
             WenXinEnum::ERNIE_BOT_TURBO  => $this->baseUrl.'/rpc/2.0/ai_custom/v1/wenxinworkshop/chat/eb-instant',
             WenXinEnum::ERNIE_BOT4       => $this->baseUrl.'/rpc/2.0/ai_custom/v1/wenxinworkshop/chat/completions_pro',
        };

        //获取key
        $keyConfig = (new KeyPoolCache($this->chatKey))->getKey();
        if(empty($keyConfig)){
            throw new Exception('请在后台配置相关key');
        }
        $this->apiKey = $keyConfig['key'];
        $this->secretKey = $keyConfig['secret'];
        $this->tokenCacheTag = $this->tokenTag.md5($this->apiKey.$this->secretKey);
        //获取access_token
        $this->getAccessToken();
        $this->baseUrl .= '?access_token='.$this->accessToken;
        $this->header = [
            'Content-Type: application/json',
        ];
    }


    /**
     * @notes 鉴权
     * @return bool|void
     * @throws Exception
     * @author ljj
     * @date 2023/8/16 4:58 下午
     */
    private function getAccessToken()
    {
        //读取缓存
        $tokenCache = Cache::get($this->tokenCacheTag);
        if ($tokenCache) {
            $this->accessToken = $tokenCache['access_token'];
            return true;
        }
        $url = 'https://aip.baidubce.com/oauth/2.0/token?grant_type=client_credentials&client_id='.$this->apiKey.'&client_secret='.$this->secretKey;
        $options['timeout'] = 20;
        $options['verify'] = false;
        $header = [
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
        ];
        $response = Requests::post($url,$header,[],$options);
        $result = json_decode($response->body,true);
        if (isset($result['error'])) {
            throw new Exception('文心一言:'.$result['error_description'] ?? '鉴权失败');
        }
        $this->accessToken = $result['access_token'];
        $result['expires_time'] = time() + $result['expires_in'];
        //缓存access_token
        Cache::set($this->tokenCacheTag,$result);
    }

    /**
     * @notes 翻译
     * @param string $prompt
     * @return string
     * @throws Exception
     * @author cjhao
     * @date 2023/11/2 12:12
     */
    public function translate(string $prompt)
    {
        $data = [
            'system'                => '你现在担任我的翻译助理，我会用任何语言和你交流，你只需将我的话翻译为英语，我会将翻译的结果用于如stable diffusion，midjournev等生成图片，所以请确保意思不变，但更适合此类场景。请按我接下来的要求返回内容',
            'messages'              => [
                [
                    "role"      => 'user',
                    'content'   => $prompt,
                ],
            ],
            'stream'                => false ,
            'temperature'           => (float)$this->temperature,
        ];
        //设置超时时间
        $options = [];
        $options['timeout'] = 20;
        $options['verify'] = false;
        $response = Requests::post($this->baseUrl, $this->header,json_encode($data),$options);
        $content = $this->getResponseData($response);
        if(empty($content)){
            throw new Exception('获取数据失败');
        }
        return $content;
    }

    /**
     * @notes 解析响应数据
     * @param $response
     * @return string
     * @author cjhao
     * @date 2023/11/2 11:42
     */
    public function getResponseData($response)
    {
        $data = $response->body;
        $data = @json_decode($data);
        if (isset($data->error_code)) {
            $this->checkErrorCode($data);
            throw new Exception($data->error_msg);
        }
        return $data->result;
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
        $data = [
            'messages'              => $messages,
            'stream'                => true ,
            'temperature'           => (float)$this->temperature,
        ];
        if($this->system){
            $data['system'] = $this->system;
        }
        $response = true;
        $callback = function ($ch, $data) use (&$content,&$response,&$total){
            $result = @json_decode($data);
            if (isset($result->error_code)) {
                $response = $result->error_msg;
                $this->checkErrorCode($result);
                //处理报错key
                (new KeyPoolCache($this->chatKey))->headerErrorKey($result->error_msg,$this->baseUrl);
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
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->header);
        curl_setopt($ch, CURLOPT_TIMEOUT,100);//设置100秒超时
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, $callback);
        curl_exec($ch);
        curl_close($ch);

        if(true !== $response){
            throw new Exception('文心一言:'.$response);
        }
        return $this;
    }

    /**
     * @notes 处理错误码
     * @param array $response
     * @return void
     * @author cjhao
     * @date 2023/10/27 10:18
     */
    public function checkErrorCode($response)
    {
        //错误码100、110、111是access_token失效问题，需要清掉access_token
        if(in_array($response->error_code,[100,110,111])){
            Cache::clear($this->tokenCacheTag);
        }
    }

    /**
     * @notes 解析文心一言流数据
     * @param $data
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
            $data = str_replace("data: ", "", $data);

            $data = json_decode($data, true);
            //解析到数据是空的、可能是数据丢失问题
            if (empty($data) || !is_array($data)) {
                continue;
            }

            $index = 0;
            $finishReason = false;
            $streamContent = $data['result'] ?? '';
            $id = $data['id'] ?? '';
            $isEnd = $data['is_end'] ?? '';
            $needClearHistory = $data['need_clear_history'] ?? '';
            if(true === $isEnd || true === $needClearHistory){
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
     * @notes 获取返回内容
     * @return mixed
     * @author ljj
     * @date 2023/8/16 5:54 下午
     */
    public function getReplyContent():array
    {
        return $this->content;
    }

    /**
     * @notes 获取token统计信息
     * @return array
     * @author ljj
     * @date 2023/8/16 5:56 下午
     */
    public function getUsage()
    {
        return $this->usage;
    }

    /**
     * @notes 获取联系上下文数量
     * @return array|mixed
     * @author ljj
     * @date 2023/8/16 6:00 下午
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
    }

    /**
     * @notes 话题属性
     * @param $temperature
     * @author cjhao
     * @date 2023/6/13 17:31
     */
    public function setPresencePenalty($presencePenalty)
    {
        $this->presence_penalty = $presencePenalty;
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
     * @notes 设置指令
     * @param $system
     * @return void
     * @author cjhao
     * @date 2023/12/7 12:00
     */
    public function setSystem($system)
    {
        $this->system = $system;
    }
}
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
use app\common\enum\chat\XingHuoEnum;
use think\Exception;
use WebSocket\Client;
use WebSocket\ConnectionException;


/**
 * 科大讯飞chat服务类
 * Class XingHuoService
 * @package app\common\service\xunfeiai
 */
class XingHuoService implements ChatInterface
{

    protected $baseUrl              = "wss://spark-api.xf-yun.com";
    protected $domain               = '';
    protected $chatKey              = '';
    protected $model                = '';
    protected $config               = [];
    protected $appid                = '';
    protected $apiSecret            = '';
    protected $apikey               = '';
    protected $temperature          = XingHuoEnum::DAFAULT_CONFIG['temperature'];       //词汇属性
    protected $contextNum           = XingHuoEnum::DAFAULT_CONFIG['context_num'];       //联系上下文
    protected $topK                 = XingHuoEnum::DAFAULT_CONFIG['top_p'];             //话题属性
    protected $content              = '';                                               //返回数据
    protected $requireBody          = '';                                               //请求体
    protected $authAddr             = '';                                               //请求地址，授权信息
    protected $usage                = [];                                               //返回token使用量



    public function __construct(array $config)
    {
        $this->config = $config;
        //联系上下文
        $this->contextNum       = $this->config['context_num'];
        //词汇属性
        $this->temperature      = (float)$this->config['temperature'];
        //随机属性
        $this->topK             = (int)$this->config['top_p'];

        //指定访问的领域
        $this->chatKey          = $this->config['key'] ?? ChatEnum::XINGHUO;
        //模型
        $this->model            = $this->config['model'];

        //获取key
        $keyConfig = (new KeyPoolCache($this->chatKey))->getKey();
        if(empty($keyConfig)){
            throw new Exception('请在后台配置相关key');
        }
        $this->appid = $keyConfig['appid'];
        $this->apikey = $keyConfig['key'];
        $this->apiSecret = $keyConfig['secret'];
        //拼接url
        switch ($this->model){
            case XingHuoEnum::XINGHUO15:
                //请求地址
                $this->baseUrl .= '/v1.1/chat';
                //指定访问的领域
                $this->domain = 'general';
                break;
            case XingHuoEnum::XINGHUO20:
                $this->baseUrl .= '/v2.1/chat';
                $this->domain = 'generalv2';
                break;
            case XingHuoEnum::XINGHUO30:
                $this->baseUrl .= '/v3.1/chat';
                $this->domain = 'generalv3';
                break;
            case XingHuoEnum::XINGHUO35:
                $this->baseUrl .= '/v3.5/chat';
                $this->domain = 'generalv3.5';
                break;
        }
        //鉴权
        $this->assembleAuthUrl();

    }


    /**
     * @notes chat请求流
     * @param array $params
     * @throws Exception
     * @author cjhao
     * @date 2023/4/24 11:42
     */
    public function chatStreamRequest(array $messages):self
    {
        try{
            ignore_user_abort(true);
            //创建ws连接对象
            $websoket = new Client($this->authAddr);
            if(empty($websoket)){
                throw new Exception('星火:WebSocket服务器连接失败');
            }
            // 发送数据到 WebSocket 服务器
            $this->getBodyData($messages);
            $websoket->send($this->requireBody);

            // 从 WebSocket 服务器接收数据
            while(true){
                $response = $websoket->receive();
                $resp = json_decode($response,true);
                if(!is_array($resp)){
                    throw new Exception('星火:数据解析异常');
                }
                $chatEvent = 'chat';
                $id = $resp['header']['sid'];
                $code = $resp['header']['code'];
                if(0 != $code){
                    //处理报错key
                    (new KeyPoolCache($this->chatKey))->headerErrorKey($resp['header']['message'],$this->baseUrl);
                    throw new Exception($resp['header']['message'] ?? '');
                }
                $status = $resp['header']['status'];
                $streamContent = $resp['payload']['choices']['text'][0]['content'];
                $this->content.=$streamContent;
                if(2 == $status){
                    $this->usage = $resp['payload']['usage'];
                    $chatEvent = 'finish';
                }
                AiChatService::parseReturnStream($chatEvent,$id,$streamContent,0,$this->chatKey);
                //断开循环
                if(2 == $status){
                    break;
                }
                if(connection_aborted()){
                    break;
                }
            }
        }catch (Exception $e){
            throw new Exception('鉴权失败,请联系管理员检查key,错误信息:'.$e->getMessage());
        } finally {
            $websoket->close();
        }

        return $this;
    }

    /**
     * @notes 鉴权
     * @param string $method
     * @return string
     * @author cjhao
     * @date 2023/8/3 11:38
     */
    public function assembleAuthUrl($method = "GET") {
        if ($this->apikey == "" && $this->apiSecret == "") { // 不鉴权
            throw new Exception('APIKey或APISecret缺少');
        }
        $ul = parse_url($this->baseUrl); // 解析地址
        if (false === $ul) { // 地址不对，也不鉴权
            throw new Exception('请求域名解析失败');
        }
        $timestamp = time();
        $rfc1123Format = gmdate("D, d M Y H:i:s \G\M\T", $timestamp);
        // 参与签名的字段 host, date, request-line
        $signString = ["host: " . $ul["host"], "date: " . $rfc1123Format, $method . " " . $ul["path"] . " HTTP/1.1"];

        // 对签名字符串进行排序，确保顺序一致
        // ksort($signString);

        // 将签名字符串拼接成一个字符串
        $sgin = implode("\n", $signString);

        // 对签名字符串进行HMAC-SHA256加密，得到签名结果
        $sha = hash_hmac('sha256', $sgin, $this->apiSecret,true);
        $signatureShaBase64 = base64_encode($sha);

        // 将API密钥、算法、头部信息和签名结果拼接成一个授权URL
        $authUrl = "api_key=\"$this->apikey\", algorithm=\"hmac-sha256\", headers=\"host date request-line\", signature=\"$signatureShaBase64\"";
        // 对授权URL进行Base64编码，并添加到原始地址后面作为查询参数
        $this->authAddr = $this->baseUrl . '?' . http_build_query([
                'host' => $ul['host'],
                'date' => $rfc1123Format,
                'authorization' => base64_encode($authUrl),
            ]);
        return $this->authAddr;
    }

    /**
     * @notes 封装请求数据
     * @param array $messages
     * @author cjhao
     * @date 2023/8/3 14:22
     */
    public function getBodyData(array $messages)
    {
        $header = [
            "app_id" => $this->appid,
        ];
        $parameter = [
            "chat" => [
                "domain" => $this->domain,
                "temperature" => $this->temperature,
//                "top_k"     => $this->topP,
            ]
        ];
        $payload = [
            "message" => [
                "text" =>$messages
            ]
        ];
        $this->requireBody = json_encode([
            "header" => $header,
            "parameter" => $parameter,
            "payload" => $payload,
        ]);
    }


    /**
     * @notes 获取内容
     * @return string
     * @author cjhao
     * @date 2023/6/19 19:06
     */
    public function getReplyContent():string
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
        $this->topK = (int)$topP;
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
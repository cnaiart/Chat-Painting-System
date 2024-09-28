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
class HunYuanService implements ChatInterface
{
    protected $baseUrl              = 'hunyuan.tencentcloudapi.com';
    protected $appid                = '';
    protected $secretId             = '';
    protected $secretKey            = '';
    protected $model                = '';
    protected $config               = [];
    protected $topP                 = '';
    protected $temperature          = '';
    protected $contextNum           = '';
    protected $chatKey              = '';
    protected $content              = [];
    protected $tokenTag             = 'hunyuan_access_token_';


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
        $keyConfig = (new KeyPoolCache($this->chatKey))->getKey();
        if(empty($keyConfig)){
            throw new Exception('请在后台配置相关key');
        }
        $this->appid = $keyConfig['appid'];
        $this->secretId = $keyConfig['secret'];
        $this->secretKey = $keyConfig['key'];
    }


    /**
     * @notes 拼接 Authorization
     * @param $data //请求正文
     * @param $service //具体产品名，通常为域名前缀
     * @param $time //当前 UNIX 时间戳
     * @param $date //当前 UNIX 时间戳 转换的 UTC 标准时间的日期
     * @return string
     * @author ljj
     * @date 2023/12/21 6:36 下午
     */
    private function getAuthorization(string $data,$service,$time,$date)
    {
        $algorithm = "TC3-HMAC-SHA256";
        //计算签名
        $canonicalHeaders = implode("\n", [
//            "content-type:application/json; charset=utf-8",
            "content-type:application/json",//去掉charset=utf-8，否则会签名错误
            "host:".$this->baseUrl,
            "x-tc-action:".strtolower($this->model),
            ""
        ]);
        $signedHeaders = implode(";", [
            "content-type",
            "host",
            "x-tc-action",
        ]);
        $credentialScope = $date."/".$service."/tc3_request";
        $canonicalRequest = "POST\n"
            ."/\n"
            ."\n"
            .$canonicalHeaders."\n"
            .$signedHeaders."\n"
            .hash("SHA256", $data);
        $stringToSign = $algorithm."\n"
            .$time."\n"
            .$credentialScope."\n"
            .hash("SHA256", $canonicalRequest);
        $secretDate = hash_hmac("SHA256", $date, "TC3".$this->secretKey, true);
        $secretService = hash_hmac("SHA256", $service, $secretDate, true);
        $secretSigning = hash_hmac("SHA256", "tc3_request", $secretService, true);
        $signature = hash_hmac("SHA256", $stringToSign, $secretSigning);

        //组装Authorization
        return $algorithm." Credential=".$this->secretId."/".$credentialScope.", SignedHeaders=".$signedHeaders.", Signature=".$signature;
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
        $time = time();//当前 UNIX 时间戳
        $date = gmdate("Y-m-d", $time);//当前 UNIX 时间戳 转换的 UTC 标准时间的日期
        $service = 'hunyuan';//具体产品名，通常为域名前缀
        $version = '2023-09-01'; //API 的版本
        $region = 'ap-guangzhou';//地域参数
        $messageLists = [];
        //数据特殊处理
        foreach ($messages as $message){
            $messageLists[] = [
                'Role'      => $message['role'],
                'Content'   => $message['content'],
            ];
        }
        $data = [
//            'TopP' => (float)$this->topP,
            'Temperature' => (float)$this->temperature,
            'Messages' => $messageLists,
        ];
        $authorization = $this->getAuthorization(json_encode($data),$service,$time,$date);
        $header = [
            'Content-Type: application/json',
            'Host: '.$this->baseUrl,
            'X-TC-Action: '.$this->model,
            'X-TC-Region: '.$region,
            'X-TC-Timestamp: '.$time,
            'X-TC-Version: '.$version,
            'Authorization: '.$authorization
        ];
        $response = true;
        $callback = function ($ch, $data) use (&$content,&$response,&$total){
            $result = @json_decode($data);
            if (isset($result->Response->Error)) {
                $response = $result->Response->Error->Code.'：'.$result->Response->Error->Message;
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
        curl_setopt($ch, CURLOPT_URL, 'https://'.$this->baseUrl);
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
            throw new Exception('腾讯混元:'.$response);
        }
        return $this;
    }

    /**
     * @notes 解析腾讯混元流数据
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
            $data = str_replace("data:", "", $data);

            $data = json_decode($data, true);
            //解析到数据是空的、可能是数据丢失问题
            if (empty($data) || !is_array($data)) {
                continue;
            }

            $index = 0;
            $finishReason = false;
            $id = '';
            $streamContent = $data['Choices'][0]['Delta']['Content'] ?? '';
            $isEnd = $data['Choices'][0]['FinishReason'] ?? '';
            if('stop' === $isEnd){
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
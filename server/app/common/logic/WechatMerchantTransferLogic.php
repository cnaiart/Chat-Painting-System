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

namespace app\common\logic;



use app\common\enum\user\UserTerminalEnum;
use app\common\model\distribution\WithdrawApply;
use app\common\model\user\UserAuth;
use app\common\service\wechat\WeChatConfigService;

/**
 * 功能: 商家转账到零钱
 * 用途：商户可以通过该接口同时向多个用户微信零钱进行转账操作。
 * 证书：需要
 * 请求URL：https://pay.weixin.qq.com/docs/merchant/apis/batch-transfer-to-balance/transfer-batch/initiate-batch-transfer.html
 * 失败后一定要用【原来的商户订单号】去重试，不然有可能存在重复支付的风险！！！
 * 转账批次单中涉及金额的字段单位为“分”
 * 成功受理商家转账请求后，可调用《商家明细单号查询明细单》接口来判断转账明细列表状态
 */
class WechatMerchantTransferLogic
{
    /**
     * @notes 配置信息
     * @param $user_id
     * @author ljj
     * @date 2023/6/20 4:59 下午
     */
    private static function config($user_id)
    {
        // 用户授权信息
        $userAuth = UserAuth::where('user_id', $user_id)->order('terminal', 'asc')->findOrEmpty();
        if($userAuth->isEmpty()) {
            throw new \think\Exception('用户未绑定微信账号');
        }

        $config = WeChatConfigService::getPayConfigByTerminal($userAuth->terminal);
        switch ($userAuth->terminal) {
            case UserTerminalEnum::WECHAT_MMP:
                $config['app_id'] = WeChatConfigService::getMnpConfig()['app_id'];
                break;
            case UserTerminalEnum::WECHAT_OA:
            case UserTerminalEnum::H5:
            case UserTerminalEnum::PC:
                $config['app_id'] = WeChatConfigService::getOaConfig()['app_id'];
                break;
            case UserTerminalEnum::IOS:
            case UserTerminalEnum::ANDROID:
                $config['app_id'] = WeChatConfigService::getOpConfig()['app_id'];
                break;
            default:
                throw new \Exception('参数错误');
        }
        $config['openid'] = $userAuth->openid;

        return $config;
    }

    /**
     * @notes 商家转账到零钱
     * @param $withdrawApply
     * @return bool
     * @throws \think\Exception
     * @author ljj
     * @date 2023/6/20 5:03 下午
     */
    public static function transfer($withdrawApply)
    {
        $config = self::config($withdrawApply['user_id']);

        //请求URL
        $url = 'https://api.mch.weixin.qq.com/v3/transfer/batches';
        //请求方式
        $http_method = 'POST';
        //请求参数
        $data = [
            'appid' => $config['app_id'],//申请商户号的appid或商户号绑定的appid（企业号corpid即为此appid）
            'out_batch_no' => $withdrawApply['batch_no'],//商户系统内部的商家批次单号，要求此参数只能由数字、大小写字母组成，在商户系统内部唯一
            'batch_name' => '提现至微信零钱',//该笔批量转账的名称
            'batch_remark' => '提现至微信零钱',//转账说明，UTF8编码，最多允许32个字符
            'total_amount' => intval(strval($withdrawApply['left_money'] * 100)),//转账金额单位为“分”。转账总金额必须与批次内所有明细转账金额之和保持一致，否则无法发起转账操作
            'total_num' => 1,//一个转账批次单最多发起三千笔转账。转账总笔数必须与批次内所有明细之和保持一致，否则无法发起转账操作
            'transfer_detail_list' => [
                [//发起批量转账的明细列表，最多三千笔
                    'out_detail_no' => $withdrawApply['sn'],//商户系统内部区分转账批次单下不同转账明细单的唯一标识，要求此参数只能由数字、大小写字母组成
                    'transfer_amount' => intval(strval($withdrawApply['left_money'] * 100)),//转账金额单位为分
                    'transfer_remark' => '提现至微信零钱',//单条转账备注（微信用户会收到该备注），UTF8编码，最多允许32个字符
                    'openid' => $config['openid'],//openid是微信用户在公众号appid下的唯一用户标识（appid不同，则获取到的openid就不同），可用于永久标记一个用户
                ]]
        ];
        if ($withdrawApply['left_money'] >= 2000) {
            if (empty($withdrawApply['real_name'])) {
                throw new \Exception('转账金额 >= 2000元，收款用户真实姓名必填');
            }
            $data['transfer_detail_list'][0]['user_name'] = self::getEncrypt($withdrawApply['real_name'],$config);
        }

        $token  = self::token($url,$http_method,$data,$config);//获取token
        $result = self::https_request($url,json_encode($data),$token);//发送请求
        $result_arr = json_decode($result,true);

        if(!isset($result_arr['create_time'])) {//批次受理失败
            throw new \Exception($result_arr['message']);
        }

        //批次受理成功，更新提现申请单为提现中状态
        WithdrawApply::update([
            'transfer_result' => $result,
        ],['id'=>$withdrawApply['id']]);

        return true;
    }

    /**
     * @notes 签名生成
     * @param $url
     * @param $http_method
     * @param $data
     * @param $config
     * @return string
     * @author ljj
     * @date 2023/6/20 5:03 下午
     */
    public static function token($url,$http_method,$data,$config)
    {
        $timestamp   = time();//请求时间戳
        $url_parts   = parse_url($url);//获取请求的绝对URL
        $nonce       = $timestamp.rand('10000','99999');//请求随机串
        $body        = empty($data) ? '' : json_encode((object)$data);//请求报文主体
        $stream_opts = [
            "ssl" => [
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ]
        ];

        $apiclient_cert_arr = openssl_x509_parse(file_get_contents($config['certificate'],false, stream_context_create($stream_opts)));
        $serial_no          = $apiclient_cert_arr['serialNumberHex'];//证书序列号
        $mch_private_key    = file_get_contents($config['private_key'],false, stream_context_create($stream_opts));//密钥
        $merchant_id = $config['mch_id'];//商户id
        $canonical_url = ($url_parts['path'] . (!empty($url_parts['query']) ? "?${url_parts['query']}" : ""));
        $message = $http_method."\n".
            $canonical_url."\n".
            $timestamp."\n".
            $nonce."\n".
            $body."\n";
        openssl_sign($message, $raw_sign, $mch_private_key, 'sha256WithRSAEncryption');
        $sign = base64_encode($raw_sign);//签名
        $schema = 'WECHATPAY2-SHA256-RSA2048';
        $token = sprintf('mchid="%s",nonce_str="%s",timestamp="%d",serial_no="%s",signature="%s"',
            $merchant_id, $nonce, $timestamp, $serial_no, $sign);//微信返回token
        return $schema.' '.$token;
    }

    /**
     * @notes 发送请求
     * @param $url
     * @param $data
     * @param $token
     * @return bool|string
     * @author ljj
     * @date 2023/6/20 5:04 下午
     */
    public static function https_request($url,$data,$token)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, (string)$url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //添加请求头
        $headers = [
            'Authorization:'.$token,
            'Accept: application/json',
            'Content-Type: application/json; charset=utf-8',
            'User-Agent:Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36',
        ];
        if(!empty($headers)){
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

    /**
     * @notes 敏感信息加解密
     * @param $str
     * @param $config
     * @return string
     * @throws \Exception
     * @author ljj
     * @date 2023/6/20 5:04 下午
     */
    public static function getEncrypt($str,$config)
    {
        //$str是待加密字符串
        $public_key = file_get_contents($config['certificate']);
        $encrypted = '';
        if (openssl_public_encrypt($str, $encrypted, $public_key, OPENSSL_PKCS1_OAEP_PADDING)) {
            //base64编码
            $sign = base64_encode($encrypted);
        } else {
            throw new \Exception('encrypt failed');
        }
        return $sign;
    }

    /**
     * @notes 商家明细单号查询明细单API
     * @param $withdrawApply
     * @return mixed
     * @throws \think\Exception
     * @author ljj
     * @date 2023/6/20 5:04 下午
     */
    public static function details($withdrawApply)
    {
        $config = self::config($withdrawApply['user_id']);
        //请求URL
        $url = 'https://api.mch.weixin.qq.com/v3/transfer/batches/out-batch-no/'.$withdrawApply['batch_no'].'/details/out-detail-no/'.$withdrawApply['sn'];
        //请求方式
        $http_method = 'GET';
        //请求参数
        $data = [];
        $token  = self::token($url,$http_method,$data,$config);//获取token
        $result = self::https_request($url,$data,$token);//发送请求
        $result_arr = json_decode($result,true);
        return $result_arr;
    }
}
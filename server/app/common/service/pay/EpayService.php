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

namespace app\common\service\pay;


use app\common\enum\PayEnum;
use app\common\enum\RefundEnum;
use app\common\enum\user\UserTerminalEnum;
use app\common\logic\PayNotifyLogic;
use app\common\model\member\MemberOrder;
use app\common\model\recharge\RechargeOrder;
use app\common\model\refund\RefundLog;
use app\common\model\refund\RefundRecord;
use app\common\service\ConfigService;
use think\facade\Log;
use think\facade\Request;

class EpayService extends BasePayService
{
    private $baseUrl;//接口地址
    private $pid;//商户ID
    private $key;//商户密钥
    private $submitUrl;//页面跳转支付url地址 此接口可用于用户前台直接发起支付，使用form表单跳转或拼接成url跳转。
    private $mapiUrl;//API接口支付url地址
    private $apiUrl;//操作订单接口url地址
    private $signType = 'MD5';//签名加密方式
    private $notifyUrl;//异步通知地址


    public function __construct()
    {
        $this->baseUrl = ConfigService::get('epay_config', 'epay_url');
        $this->pid = ConfigService::get('epay_config', 'epay_pid');
        $this->key = ConfigService::get('epay_config', 'epay_key');
//        if (empty($this->baseUrl) || empty($this->pid) || empty($this->key)) {
//            $this->setError('易支付配置错误');
//            return false;
//        }

        $this->submitUrl = $this->baseUrl.'submit.php';
        $this->mapiUrl = $this->baseUrl.'mapi.php';
        $this->apiUrl = $this->baseUrl.'api.php';
        $this->notifyUrl = (string)url('pay/notifyEpay', [], false, true);
    }

    /**
     * @notes 发起支付（API接口）
     * @param $from
     * @param $order
     * @return mixed
     * @author ljj
     * @date 2024/1/8 11:29 上午
     */
    public function apiPay($from,$order)
    {
        try {
            $param = $this->buildRequestParam($from,$order,'api');
            $response = $this->getHttpResponse($this->mapiUrl, http_build_query($param));
            $responseArr = json_decode($response, true);
            if ($responseArr['code'] != 1){
                throw new \Exception($responseArr['msg'] ?? '易支付请求支付失败');
            }
            $responseArr['pay_way'] = $order['pay_way'];
            return $responseArr;
        } catch (\Exception $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 构建请求参数
     * @param $from
     * @param $order
     * @param $requestType //请求方式：api-API接口；submit-页面跳转
     * @return mixed
     * @author ljj
     * @date 2024/1/8 11:26 上午
     */
    private function buildRequestParam($from,$order,$requestType)
    {
        $query = http_build_query(['checkPay'=>true,'id'=>$order['id'],'from'=>$from]);
        $returnUrl = request()->domain(true).'/mobile'.$order['redirect_url'].'?'.$query;
        if (UserTerminalEnum::PC == $order['user_terminal']) {
            $returnUrl = request()->domain(true).$order['redirect_url'].'&'.$query;
        }

        $param = [
            "pid" => $this->pid,//商户ID
            "type" => $this->getPayType($order['pay_way']),//支付方式
            "out_trade_no" => $order['sn'],//商户订单号
            "notify_url" => $this->notifyUrl,//异步通知地址
            "return_url" => $returnUrl,//跳转通知地址
            "name" => $this->getPayDesc($from),//商品名称
            "money" => $order['order_amount'],//单位：元，最大2位小数
            'param' => $from//业务扩展参数
        ];
        switch ($requestType) {
            case 'api':
                $param['clientip'] = Request::ip();//用户IP地址
                $param['device'] = $order['device'];//设备类型
                break;
        }

        $mysign = $this->getSign($param);
        $param['sign'] = $mysign;
        $param['sign_type'] = $this->signType;
        return $param;
    }

    /**
     * @notes 计算签名
     * @param $param
     * @return string
     * @author ljj
     * @date 2024/1/8 11:27 上午
     */
    private function getSign($param)
    {
        ksort($param);
        reset($param);
        $signstr = '';

        foreach($param as $k => $v){
            if($k != "sign" && $k != "sign_type" && $v!=''){
                $signstr .= $k.'='.$v.'&';
            }
        }
        $signstr = substr($signstr,0,-1);
        $signstr .= $this->key;
        $sign = md5($signstr);
        return $sign;
    }

    /**
     * @notes 请求外部资源
     * @param $url
     * @param false $post
     * @param int $timeout
     * @return bool|string
     * @author ljj
     * @date 2024/1/8 11:27 上午
     */
    private function getHttpResponse($url, $post = false, $timeout = 10)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $httpheader[] = "Accept: */*";
        $httpheader[] = "Accept-Language: zh-CN,zh;q=0.8";
        $httpheader[] = "Connection: close";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if($post){
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    /**
     * @notes 获取支付方式
     * @param $payWay
     * @return string
     * @author ljj
     * @date 2024/1/8 11:50 上午
     */
    public function getPayType($payWay)
    {
        $result = '';
        switch ($payWay) {
            case PayEnum::WECHAT_EPAY:
                $result = 'wxpay';
                break;
            case PayEnum::ALI_EPAY:
                $result = 'alipay';
                break;
            default:
                throw new \Exception('支付方式错误');
        }
        return $result;
    }

    /**
     * @notes 获取支付信息
     * @param $from
     * @return string
     * @author ljj
     * @date 2024/1/8 12:13 下午
     */
    public function getPayDesc($from)
    {
        $desc = [
            'member' => '会员',
            'recharge' => '充值',
        ];
        return $desc[$from] ?? '商品';
    }

    /**
     * @notes 异步回调验证
     * @param $data
     * @return bool
     * @author ljj
     * @date 2024/1/8 2:34 下午
     */
    public function verifyNotify($data)
    {
        if(empty($data)) {
            return false;
        }

        $sign = $this->getSign($data);

        if($sign === $data['sign']){
            $signResult = true;
        }else{
            $signResult = false;
        }

        return $signResult;
    }

    /**
     * @notes 支付回调
     * @param $data
     * @return bool
     * @author ljj
     * @date 2024/1/8 2:46 下午
     */
    public function notify($data)
    {
        try {
            $verify = $this->verifyNotify($data);
            if (false === $verify) {
                throw new \Exception('异步通知验签失败');
            }
            if ($data['trade_status'] !== 'TRADE_SUCCESS') {
                throw new \Exception('易支付异步通知返回支付状态码：'.$data['trade_status']);
            }

            //验证订单是否已支付
            $extra['transaction_id'] = $data['trade_no'];
            $data['out_trade_no'] = mb_substr($data['out_trade_no'], 0, 18);
            switch ($data['param']) {
                case 'recharge':
                    $order = RechargeOrder::where(['sn' => $data['out_trade_no']])->findOrEmpty();
                    if($order->isEmpty() || $order->pay_status == PayEnum::ISPAID) {
                        return true;
                    }
                    PayNotifyLogic::handle('recharge', $data['out_trade_no'], $extra);
                    break;
                case 'member':
                    $order = MemberOrder::where(['sn' => $data['out_trade_no']])->findOrEmpty();
                    if($order->isEmpty() || $order->pay_status == PayEnum::ISPAID) {
                        return true;
                    }
                    PayNotifyLogic::handle('member', $data['out_trade_no'], $extra);
                    break;
            }

            return true;
        } catch (\Exception $e) {
            $record = [
                __CLASS__,
                __FUNCTION__,
                $e->getFile(),
                $e->getLine(),
                $e->getMessage()
            ];
            Log::write(implode('-', $record));
            $this->setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 订单退款
     * @param $order
     * @param $refundAmount
     * @return mixed
     * @author ljj
     * @date 2024/1/15 5:37 下午
     */
    public function refund($order, $refundAmount)
    {
        $url = $this->apiUrl.'?act=refund';
        $post = 'pid=' . $this->pid . '&key=' . $this->key . '&trade_no=' . $order['transaction_id'] . '&money=' . $refundAmount;
        $response = $this->getHttpResponse($url, $post);
        $responseArr = json_decode($response, true);
        return $responseArr;
    }



    // 发起支付（页面跳转）
    public function pagePay($from,$order)
    {
        $param = $this->buildRequestParam($from,$order,'pagePay');

        $html = '<form id="dopay" action="'.$this->submitUrl.'" method="post">';
        foreach ($param as $k=>$v) {
            $html.= '<input type="hidden" name="'.$k.'" value="'.$v.'"/>';
        }
        $html .= '<input type="submit" value="正在跳转"></form><script>document.getElementById("dopay").submit();</script>';

        return [
            'config' => $html,
            'pay_way' => $order['pay_way']
        ];
    }

    // 发起支付（获取链接）
    public function getPayLink($param_tmp){
        $param = $this->buildRequestParam($param_tmp);
        $url = $this->submitUrl.'?'.http_build_query($param);
        return $url;
    }

    // 同步回调验证
    public function verifyReturn(){
        if(empty($_GET)) return false;

        $sign = $this->getSign($_GET);

        if($sign === $_GET['sign']){
            $signResult = true;
        }else{
            $signResult = false;
        }

        return $signResult;
    }

    // 查询订单支付状态
    public function orderStatus($trade_no){
        $result = $this->queryOrder($trade_no);
        if($result['status']==1){
            return true;
        }else{
            return false;
        }
    }

    // 查询订单
    public function queryOrder($trade_no){
        $url = $this->apiUrl.'?act=order&pid=' . $this->pid . '&key=' . $this->key . '&trade_no=' . $trade_no;
        $response = $this->getHttpResponse($url);
        $arr = json_decode($response, true);
        return $arr;
    }
}
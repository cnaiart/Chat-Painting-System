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
namespace app\common\service\voice;


use app\common\cache\KeyPoolCache;
use app\common\enum\KeyPoolEnum;
use app\common\enum\voice\VoiceEnum;
use CURLFile;
use think\Exception;
use think\facade\Log;
use WpOrg\Requests\Requests;

/**
 * oepnai语音服务类
 * Class VoiceConfigService
 * @package app\common\service\voice
 */
class OpenaiVoiceService
{

    /**
     * @notes 语音合成
     * @param $key
     * @param $channelConfig
     * @param $content
     * @return string
     * @author cjhao
     * @date 2024/2/20 10:05
     */
    public function voiceGenerate(array $channelConfig, string $content)
    {
        $keyPoolCache = (new KeyPoolCache(VoiceEnum::OPENAI,KeyPoolEnum::TYPE_VOICE));
        $apiKey = $keyPoolCache->getKey();
        if(empty($apiKey)){
            throw new Exception('请在后台配置key');
        }
        //参数
        $headers['Content-Type'] = 'application/json';
        $headers['Authorization'] = 'Bearer '.$apiKey;
        $baseUrl = $channelConfig['agency_api'].'/v1/audio/speech';
        $data = [
            'model'             => $channelConfig['model'], //聊天模型
            'input'             => $content,
            'voice'             => $channelConfig['pronounce'],
            'response_format'   => 'mp3',
            'speed'             => $channelConfig['speed'],
        ];
        $options = [];
        //设置超时时间
        $options['timeout'] = 20;
        //不校验证书
        $options['verify'] = false;
        $response = Requests::post($baseUrl,$headers,json_encode($data),$options);
        $responseData = json_decode($response->body,true);
        if(isset($responseData['error'])){
            //自动下架
            $keyPoolCache->headerErrorKey($response['error']['message'] ?? '',$baseUrl);
            throw new Exception($responseData['error']['message'] ? : $responseData['error']['type']);
        }
        return $response->body;
    }


    /**
     * @notes 语音转写
     * @param string $key
     * @param array $channelConfig
     * @param string $fileUrl
     * @return string
     * @author cjhao
     * @date 2024/2/20 17:17
     */
    public function voiceTransfer(array $channelConfig,string $fileUrl)
    {
        $keyPoolCache = (new KeyPoolCache(VoiceEnum::OPENAI,KeyPoolEnum::TYPE_VOICE_INPUT));
        $apiKey = $keyPoolCache->getKey();
        //key池获取通道的key
        if(empty($apiKey)){
            throw new Exception('请在后台配置key');
        }
        $header = [
            'Content-Type:multipart/form-data',
            'Authorization: Bearer '.$apiKey,
        ];
        $baseUrl = $channelConfig['agency_api'].'/v1/audio/transcriptions';
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $baseUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => [
                'file'  => new CURLFile($fileUrl),
                'language'  =>'zh',
                'prompt'    => '如翻译的内容是中文，请将内容翻译成简体中文',
                'model' => 'whisper-1'
            ]
        ]);
        $data = curl_exec($curl);
        $response = json_decode($data,true);

        if(isset($response['error'])){
            //自动下架
            $keyPoolCache->headerErrorKey($response['error']['message'] ?? '',$baseUrl);
            throw new Exception($response['error']['message'] ? : $response['error']['type']);
        }
        return $response['text'];

    }
}
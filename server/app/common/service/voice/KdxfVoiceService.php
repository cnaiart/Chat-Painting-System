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
use IFlytek\Xfyun\Speech\LfasrClient;
use IFlytek\Xfyun\Speech\TtsClient;
use Exception;

/**
 * 科大讯飞语音服务类
 * Class VoiceConfigService
 * @package app\common\service\voice
 */
class KdxfVoiceService
{

    /**
     * @notes 语音合成
     * @param array $channelConfig
     * @param string $content
     * @return string
     * @throws \Exception
     * @author cjhao
     * @date 2024/2/20 10:05
     */
    public function voiceGenerate(array $channelConfig,string $content)
    {
        try {
            $keyPoolCache = (new KeyPoolCache(VoiceEnum::KDXF,KeyPoolEnum::TYPE_VOICE));
            $apiKey = $keyPoolCache->getKey();
            if(empty($apiKey)){
                throw new Exception('请在后台配置key');
            }
            //参数
            $client = new TtsClient($apiKey['appid'], $apiKey['key'], $apiKey['secret'], [
                'aue'       => 'lame',
                'vcn'       => $channelConfig['pronounce'],
                'speed'     => (int)$channelConfig['speed'],
            ]);
            // 返回格式为音频文件的二进制数组，可以直接通过file_put_contents存入本地文件
            return $client->request($content)->getBody()->getContents();
        }catch (Exception $e){
            $errorData = json_decode($e->getMessage(),true);
            $message = $e->getMessage();
            if(is_array($errorData)){
                $message = $errorData['message'] ?? '';
                $keyPoolCache->headerErrorKey($errorData['message'] ?? '','');
            }
            throw new Exception($message);
        }

    }


    /**
     * @notes 语音转写
     * @param array $key
     * @param string $fileUrl
     * @return string
     * @throws Exception
     * @author cjhao
     * @date 2024/2/20 10:27
     */
    public function voiceTransfer(string $fileUrl)
    {
        try {
            $keyPoolCache = (new KeyPoolCache(VoiceEnum::KDXF,KeyPoolEnum::TYPE_VOICE_INPUT));
            $apiKey = $keyPoolCache->getKey();
            if(empty($apiKey)){
                throw new Exception('请在后台配置key');
            }
            $client = new LfasrClient($apiKey['appid'], $apiKey['key']);
            $taskId = $client->combineUpload($fileUrl);
            do {
                $progress = json_decode($client->getProgress($taskId)->getBody()->getContents(), true);
                if ($progress['ok'] !== 0) {
                    throw new Exception('音频识别失败:'.$progress['failed']);
                }
                $data = json_decode($progress['data'], true);
                if (9 == $data['status']) {
                    break;
                }
                //延迟500毫米执行查询
//                usleep(500000);
            } while (true);
            $result = $client->getResult($taskId)->getBody()->getContents();
            $result = json_decode($result,true);
            $transferData = json_decode($result['data'],true);
            $transferText = '';
            foreach ($transferData as $data){
                $transferText .= $data['onebest'];
            }
            return $transferText;
        }catch (Exception $e){
            $errorData = json_decode($e->getMessage(),true);
            $message = $e->getMessage();
            //自动下架
            if(is_array($errorData)){
                $message = $errorData['message'] ?? '';
                $keyPoolCache->headerErrorKey($errorData['message'] ?? '','');
            }
            throw new Exception($message);
        }

    }
}
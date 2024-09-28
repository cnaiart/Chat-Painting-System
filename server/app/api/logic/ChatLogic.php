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
namespace app\api\logic;
use app\common\cache\KeyPoolCache;
use app\common\enum\ChatRecordEnum;
use app\common\enum\KeyPoolEnum;
use app\common\enum\voice\VoiceEnum;
use app\common\logic\BaseLogic;
use app\common\model\ChatRecords;
use app\common\service\{chat\AiChatService,
    ConfigService,
    FileService,
    voice\KdxfVoiceService,
    voice\OpenaiVoiceService,
    voice\VoiceConfigService};
use Exception;
use think\file\UploadedFile;

/**
 * 对话逻辑类
 * Class ChatLogic
 * @package app\api\logic
 */
class ChatLogic extends BaseLogic
{

    /**
     * @notes 获取对话模型
     * @return array
     * @author cjhao
     * @date 2023/7/19 11:48
     */
    public function getModel(int $userId)
    {
        $isOpen = ConfigService::get('chat_config','billing_is_open');
        $billingModelList = [];
        if($isOpen){
            $billingModelList = AiChatService::getBillingModel($userId);
        }
        return array_values($billingModelList);
    }


    /**
     * @notes 音频合成
     * @param int $userId
     * @param array $params
     * @return false|string[]
     * @author cjhao
     * @date 2024/2/1 15:49
     */
    public function voiceGenerate(int $userId,array $params)
    {
        try {
            $type       = $params['type'] ?? VoiceEnum::VOICE_BROADCAST;
            $recordsId  = intval($params['records_id'] ?? '');
            $content    = $params['content'] ?? '';
            //验证语音配置
            $this->checVoiceConfig($type);
            //验证并获取获取对话记录
            $records = $this->checkChatRecords($userId,$recordsId,$content);
            //获取配置
            $voiceConfig = VoiceConfigService::getVoiceConfig($type);
            //生成文件
            $fileUrl = $this->generateVoiceFile($type,$records,$content,$voiceConfig);
            if(VoiceEnum::VOICE_CHAT == $type){
                $voicePlugin = $records['voice_plugin'] ?: [];
                $voicePlugin['voice_output'] = $fileUrl;
                ChatRecords::where(['id'=>$recordsId])->update(['voice_plugin'=>$voicePlugin]);
            }
            return [
                'file_url'  => FileService::getFileUrl($fileUrl)
            ];
        }catch (Exception $e){
            self::$error = $e->getMessage();
            return false;
        }

    }

    /**
     * @notes 生成音频文件
     * @param int $type
     * @param array $records
     * @param int $content
     * @param array $channelConfig
     * @return string
     * @author cjhao
     * @date 2024/2/1 15:17
     */
    public function generateVoiceFile(int $type,array $records,int $content,array $voiceConfig)
    {
        //当前存储的类型
        $storageDefaultConfig = ConfigService::get('storage', 'default', 'local');
        $channelConfig = $voiceConfig['channel_config'];
        //不同音频文件存储
        if(VoiceEnum::VOICE_BROADCAST == $type){
            //用id、配置生成md5文件名，后面通过md5判断以前是否生成过直接返回，不用在重新生成
            $filename = md5($records['id'].$content.$voiceConfig['channel'].$channelConfig['pronounce'].$channelConfig['speed']).'.mp3';
            $typeDir = ChatRecordEnum::getChatVoiceDir($records['type']);
            $saveDir = config('project.voice_config.save_dir').$typeDir;
            $fileUrl = $saveDir.'/'.$filename;
            //判断文件是否已经存在，如果不存在，重新生成
            if ($storageDefaultConfig == 'local') {
                if(file_exists($fileUrl)){
                    return $fileUrl;
                }
            }else{
                //判断远程文件是否存在
                if(getRemoteFileExists(FileService::getFileUrl($fileUrl))){
                    return $fileUrl;
                }
            }

        }else{
            $channelConfig = $voiceConfig['channel_config'];
            $filename = md5($records['id'].$content).'.mp3';
            $saveDir = config('project.voice_chat_config.save_dir'). date('Ymd');
            $fileUrl = $saveDir.'/'.$filename;
        }
        //选取AI对象
        $binaryData = match ($voiceConfig['channel']) {
            VoiceEnum::KDXF         => (new KdxfVoiceService())->voiceGenerate($channelConfig,$records['reply'][$content]),
            VoiceEnum::OPENAI       => (new OpenaiVoiceService())->voiceGenerate($channelConfig,$records['reply'][$content]),
        };
        if (!file_exists($saveDir)) {
            mkdir($saveDir, 0775, true);
        }
        //保存到本地
        file_put_contents($fileUrl,$binaryData);
        if('local' != $storageDefaultConfig){
            //如果当前文件放到当前存储上
            FileService::saveOssFile($fileUrl);
        }
        return $fileUrl;
    }


    /**
     * @notes 验证对话记录数据
     * @param int $userId
     * @param int $recordsId
     * @param int $content
     * @return array
     * @throws Exception
     * @author cjhao
     * @date 2024/1/30 12:10
     */
    public function checkChatRecords(int $userId,int $recordsId,int $content):array
    {
        if(empty($recordsId) || '' === $content){
            throw new Exception('请选择播报的对话');
        }
        $records = ChatRecords::where(['user_id'=>$userId,'id'=>$recordsId])->findOrEmpty()->toArray();
        if(empty($records)){
            throw new Exception('对话不存在');
        }
        $reply = $records['reply'][$content] ?? '';
        if(empty($reply)){
            throw new Exception('对话内容不存在');
        }

        return $records;
    }

    /**
     * @notes 语音转文字
     * @param int $userId
     * @param int $type
     * @param UploadedFile $file
     * @return false|string|array
     * @throws \Exception
     * @author cjhao
     * @date 2024/1/29 10:26
     */
    public function voiceTransfer(int $userId,int $type,UploadedFile $file)
    {
        try {
            //验证功能是否开启
            $this->checVoiceConfig($type);
            //文件名
            $fileName = md5(uniqid($userId).mt_rand(0, 99999)).'.mp3';
            //获取文件保存路径
            if(VoiceEnum::VOICE_INPUT == $type){
                $saveDir = config('project.voice_input_config.save_dir');
            }else{
                $saveDir = config('project.voice_chat_config.save_dir');
            }
            $saveDir .= date('Ymd');
            //保存到本地
            $file->move($saveDir,$fileName);
            //音频转文字
            $fileUrl = $saveDir.'/'.$fileName;
            $transferText = $this->voiceTransferText($fileUrl,$type);
            //如果文件放到当前存储上
            FileService::saveOssFile($fileUrl);
            //获取当前设置的存储，将文件放到当前存储上
            return [
                'text'  => $transferText,
                'file'  => FileService::getFileUrl($fileUrl),
            ];
        }catch (Exception $e){
            self::$error = $e->getMessage();
            return false;
        }

    }

    /**
     * @notes 将音频转成文字
     * @param string $fileUrl
     * @param int $type
     * @return string
     * @throws Exception
     * @author cjhao
     * @date 2024/1/30 15:17
     */
    public function voiceTransferText(string $fileUrl,int $type):string
    {
        if(VoiceEnum::VOICE_INPUT == $type){
            $channel =  ConfigService::get('voice_input_config','channel');
        }else{
            $channel =  ConfigService::get('voice_chat_config','channel');
        }
        $channelConfig = ConfigService::get('voice_input_config',$channel,[]);
        //选取AI对象
        $transferText = match ($channel) {
            VoiceEnum::KDXF         => (new KdxfVoiceService())->voiceTransfer($fileUrl),
            VoiceEnum::OPENAI       => (new OpenaiVoiceService())->voiceTransfer($channelConfig,$fileUrl),
        };
        return $transferText;
    }
    /**
     * @notes 验证语音播报配置
     * @param int $userId
     * @param int $recordsId
     * @param int $content
     * @return true
     * @throws Exception
     * @author cjhao
     * @date 2023/10/11 11:31
     */
    public function checVoiceConfig($type = VoiceEnum::VOICE_BROADCAST):bool
    {
        if(!in_array($type,[VoiceEnum::VOICE_BROADCAST,VoiceEnum::VOICE_INPUT,VoiceEnum::VOICE_CHAT])){
            throw new Exception('参数错误');
        }
        //验证功能是否开启
        switch ($type){
            case VoiceEnum::VOICE_BROADCAST:
                $isOpen = ConfigService::get('voice_config','is_open');
                if(0 == $isOpen){
                    throw new Exception('语音播报功能未开启');
                }
                break;
            case VoiceEnum::VOICE_INPUT:
                $isOpen = ConfigService::get('voice_input_config','is_open');
                if(0 == $isOpen){
                    throw new Exception('语音输入功能未开启');
                }
                break;
            case VoiceEnum::VOICE_CHAT:
                $isOpen = ConfigService::get('voice_chat_config','is_open');
                if(0 == $isOpen){
                    throw new Exception('语音对话功能未开启');
                }
                break;
        }
        return true;
    }

}
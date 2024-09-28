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
namespace app\adminapi\logic\setting;
use app\common\cache\KeyPoolCache;
use app\common\enum\chat\ChatEnum;
use app\common\enum\DrawEnum;
use app\common\enum\KeyPoolEnum;
use app\common\enum\qrcode\QrcodeEnum;
use app\common\enum\voice\VoiceEnum;
use app\common\logic\BaseLogic;
use app\common\model\KeyPool;
use app\common\service\chat\ChatGptService;
use app\common\service\ConfigService;
use think\Exception;

/**
 * key池逻辑类
 * Class KeyPoolLogic
 * @package app\adminapi\logic\setting
 */
class KeyPoolLogic extends BaseLogic
{

    /**
     * @notes key池规则
     * @return array
     * @author cjhao
     * @date 2023/8/16 16:33
     */
    public function getConfig()
    {
        return [
            'key_auto_down'  => ConfigService::get('chat_config','key_auto_down'),
        ];
    }

    /**
     * @notes 设置key池规则
     * @param array $params
     * @author cjhao
     * @date 2023/8/16 16:35
     */
    public function setConfig(array $params)
    {
        $keyAutoDown = $params['key_auto_down'] ?? 0;
        ConfigService::set('chat_config','key_auto_down',$keyAutoDown);
    }

    /**
     * @notes 获取
     * @return array
     * @author cjhao
     * @date 2023/7/25 14:45
     */
    public function getAiModel($type)
    {
        $chatModel          = [];
        $drawModel          = [];
        $voiceChannel       = [];
        $artCodeModel       = [];
        $voiceInputChannel  = [];
        $voiceChatChannel   = [];


        if(empty($type)){
            $chatModel          = ChatEnum::getChatName();
            $drawModel          = DrawEnum::getAiModelName();
            $voiceChannel       = VoiceEnum::getChannel();
            $voiceInputChannel  = VoiceEnum::getChannel();
            $voiceChatChannel   = VoiceEnum::getChannel();
            $artCodeModel       = QrcodeEnum::getAiModelName();
        }else{
            KeyPoolEnum::TYPE_CHAT  == $type && $chatModel = ChatEnum::getChatName();
            KeyPoolEnum::TYPE_DRAW  == $type && $drawModel = DrawEnum::getAiModelName();
            KeyPoolEnum::TYPE_VOICE == $type && $voiceChannel =  VoiceEnum::getChannel();
            KeyPoolEnum::TYPE_QRCODE == $type && $artCodeModel =  QrcodeEnum::getAiModelName();
            KeyPoolEnum::TYPE_VOICE_INPUT == $type && $artCodeModel =  VoiceEnum::getChannel();
            KeyPoolEnum::TYPE_VOICE_CHAT == $type && $artCodeModel =  VoiceEnum::getChannel();
        }
        return array_merge($chatModel,$drawModel,$voiceChannel,$voiceInputChannel,$voiceChatChannel,$artCodeModel);

    }


    /**
     * @notes 添加key
     * @param $params
     * @author cjhao
     * @date 2023/7/25 15:02
     */
    public function add(array $params)
    {
        (new KeyPoolCache($params['ai_key']))->delKey();
        KeyPool::create($params);
    }

    /**
     * @notes 更新key
     * @param $params
     * @author cjhao
     * @date 2023/7/25 15:02
     */
    public function edit(array $params)
    {
        (new KeyPoolCache($params['ai_key']))->delKey();
        //清掉停用信息
        if(isset($params['is_clear'])) {
            $params['notice'] = '';
            unset($params['is_clear']);
        }
        KeyPool::where(['id'=>$params['id']])->update([
            'appid'   => $params['appid'],
            'key'   => $params['key'],
            'secret'   => $params['secret'],
        ]);
    }

    /**
     * @notes 删除key
     * @param $params
     * @author cjhao
     * @date 2023/7/25 15:05
     */
    public function del($params)
    {
        foreach ($params['ids'] as $id) {
            $keyPool = KeyPool::where(['id'=>$id])->findOrEmpty();
            $keyPool->delete();
            (new KeyPoolCache($keyPool->ai_key))->delKey();
        }
        return true;
    }


    /**
     * @notes 修改状态
     * @author cjhao
     * @date 2023/7/25 15:06
     */
    public function status(int $id)
    {
        $keyPool = KeyPool::findOrEmpty($id);
        $keyPool->status = $keyPool->status ? 0 : 1;
        $keyPool->save();
        (new KeyPoolCache($keyPool->ai_key))->delKey();
    }


    /**
     * @notes key详情
     * @param int $id
     * @return KeyPool|array|\think\Model
     * @author cjhao
     * @date 2023/8/3 12:15
     */
    public function detail(int $id)
    {
        return KeyPool::findOrEmpty($id)->toArray();

    }

    /**
     * @notes 查询余额
     * @param int $id
     * @return array|bool
     * @author cjhao
     * @date 2023/7/25 15:23
     */
    public function queryBalance(int $id)
    {

        try{
            $keyPool = KeyPool::findOrEmpty($id);
            if($keyPool->isEmpty()){
                throw new Exception('秘钥不存在');
            }
//            if(!in_array($keyPool->ai_key,ChatEnum::OPEN_CHAT)){
//                throw new Exception('该key不支持查询余额');
//            }
            $queryBalance = (new ChatGptService())->queryKeyBalance($keyPool->key);
            return $queryBalance;
        }catch (\Exception $e){
            self::$error = $e->getMessage();
            return false;
        }
    }
}
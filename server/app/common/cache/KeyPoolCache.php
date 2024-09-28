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
namespace app\common\cache;
use app\common\enum\chat\ChatEnum;
use app\common\enum\DrawEnum;
use app\common\enum\voice\VoiceEnum;
use app\common\model\KeyDownRule;
use app\common\model\KeyPool;
use app\common\service\ConfigService;
use think\facade\Cache;
use think\facade\Db;

/**
 * keyKPool的缓存类
 * Class ChatKeyCache
 * @package app\common\cache
 */
class KeyPoolCache extends BaseCache
{

    private $cacheName = 'ai_key_';
    private $aiKey = '';
    private $cacheApiKey = [];
    private $apiKey = '';

    private $type = '';

    public function __construct($key,$type = '')
    {
        $this->aiKey = $key;
        $this->cacheName .= $key;
        if($type){
            $this->type = $type;
            $this->cacheName.= '_'.$type;
        }
    }

    /**
     * @notes 获取key
     * @return string|array
     * @author cjhao
     * @date 2023/6/19 17:15
     * 每次从缓存key数组里面拿第一个元素并移除第一个元素，
     * 再将缓存key重置，如果缓存key为空，则重新从数据库读取
     */
    public function getKey()
    {
        $cacheKey = Cache::get($this->cacheName);
        if(empty($cacheKey)){
            $type = $this->type;
            $keyPool = KeyPool::where(function ($query) use($type) {
                $query->where(['status'=>1,'ai_key'=>$this->aiKey]);
                if($type){
                    $query->where('type', $type);
                }
            })->column('key,appid,secret');
            $multiKeyModel = [
                ChatEnum::XINGHUO,
                ChatEnum::HUNYUAN,
                ChatEnum::WENXIN,
                ChatEnum::MINIMAX,
                VoiceEnum::KDXF,
                DrawEnum::API_YIJIAN_SD
            ];
            //返回多配置的key
            if(in_array($this->aiKey,$multiKeyModel)){
                $cacheKey = $keyPool;
            }else{
                $cacheKey = array_column($keyPool,'key');
            }
        }
        if(empty($cacheKey)){
            return '';
        }
        $this->cacheApiKey = $cacheKey;
        $modelKey = array_shift($this->cacheApiKey);
        $this->apiKey = $modelKey;
        //调用时间
        $where = [];
        if(is_array($modelKey)){
            foreach ($modelKey as $key => $val){
                $where[] = [$key,'=',$val];
            }
        }else{
            $where[] = ['key','=',$modelKey];
        }
        //写入调用时间
        KeyPool::where($where)->update(['use_time'=>time()]);

        //重新设置缓存
        $this->setKey();
        return $modelKey;
    }

    /**
     * @notes 设置key
     * @param $type
     * @param $cacheKey
     * @author cjhao
     * @date 2023/6/19 17:09
     */
    public function setKey($key = [])
    {
        $apiKey = $this->cacheApiKey;
        if($key){
            $apiKey = $key;
        }
        return Cache::set($this->cacheName,$apiKey);
    }


    /**
     * @notes 删除key
     * @return bool
     * @author cjhao
     * @date 2023/7/26 9:50
     */
    public function delKey()
    {
        return Cache::delete($this->cacheName);
    }


    /**
     * @notes 下架key
     * @param $errorMsg
     * @author cjhao
     * @date 2023/8/10 14:20
     */
    public function takeDownKey($errorMsg){
        $keyAutoDown = ConfigService::get('chat_config', 'key_auto_down');
        //key自动下架关闭
        if(0 == $keyAutoDown){
            return true;
        }
        $where = [];
        $where[] = ['ai_key','=',$this->aiKey];
        if(is_array($this->apiKey)){
            foreach ($this->apiKey as $item => $value){
                $where[] = [$item,'=',$value];
            }
        }else{
            $where[] = ['key','=',$this->apiKey];
        }
        $errorMsg['stop_time'] = date('Y-m-d H:i:s');
        //更新key
        KeyPool::where($where)->update([
            'status'=>0,
            'notice'=>json_encode($errorMsg,JSON_UNESCAPED_UNICODE)
        ]);
        //删除缓存
        $this->delKey();
    }


    /**
     * @notes 处理有问题的key
     * @param $errorMsg
     * @param $api
     * @author ljj
     * @date 2023/9/15 10:12 上午
     */
    public function headerErrorKey($errorMsg,$api)
    {
        $tisMsg = [];
        $key_down_rule = KeyDownRule::field('rule,prompt')->where(['status'=>1,'ai_key'=>$this->aiKey])->select()->toArray();
        if (!empty($key_down_rule)) {
            foreach ($key_down_rule as $rule) {
                if (false !== strpos($errorMsg, $rule['rule'])) {
                    $tisMsg['notice'] = $rule['prompt'].$rule['rule'];
                }
            }
        }

        if(empty($tisMsg)){
            return ;
        }
        $tisMsg['api'] = $api;
        $this->takeDownKey($tisMsg);
    }
}
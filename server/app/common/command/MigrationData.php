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
namespace app\common\command;
use app\common\enum\chat\Api2dEnum;
use app\common\enum\chat\ChatEnum;
use app\common\enum\chat\XingHuoEnum;
use app\common\enum\chat\ZhiPuEnum;
use app\common\enum\DrawEnum;
use app\common\enum\KeyPoolEnum;
use app\common\model\ChatRecords;
use app\common\model\KeyDownRule;
use app\common\model\KeyPool;
use app\common\service\ConfigService;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Exception;
use think\facade\Cache;
use think\facade\Log;

/**
 * 迁移配置
 * Class MigrationKey
 */
class MigrationData extends Command
{
    protected  $configCount = 0;
    protected  $chatGLMModel = [
        'chatGLM-Std',
        'chatGLM-Lite',
        'chatGLM-pro',
        'chatGLM-Turbo'
    ];
    protected  $gptModel = [
        'gpt3.5',
        'gpt4.0',
        'api2d3.5',
        'api2d4.0'
    ];
    protected  $wenxinModel = [
        'BAIDU'
    ];
    protected  $xinghuoModel = [
        'XUNFEI',
        'XUNFEI2.0',
        'XUNFEI3.0'
    ];
    protected function configure()
    {
        $this->setName('migration_data')
            ->setDescription('3.5.1版本迁移数据');
    }

    protected function execute(Input $input, Output $output)
    {

        try{
            //如果存在缓存，直接不执行
            $chacheMigration = Cache::get('chache_migration_data_351',0);
            if($chacheMigration){
                echo "数据已完成迁移，请勿重复执行";
                return;
            }
            //对话配置
            $this->chatConfig();
            //计费配置
            $this->billingConfig();
            //迁移key池
            $this->keyPoolConfig();
            //迁移绘画翻译
            $this->drawTranslate();
            //迁移自动下架key
            $this->chatDownKey();
            //调整对话活记录的模型
            $this->chatRecords();

            echo "数据迁移成功,共迁移".$this->configCount.'份数据';
            //迁移数据成功，加个缓存
            Cache::set('chache_migration_data_351',1);
        }catch (Exception $e){
            echo "数据迁移失败:".$e->getMessage().$e->getLine();
            $output->error('数据迁移失败:'.$e->getMessage());
            Log::write('数据迁移错误:'.$e->getLine().'-'.$e->getMessage());
        }



    }

    /**
     * @notes 更新对话模型配置
     * @return void
     * @author cjhao
     * @date 2023/12/22 15:20
     */
    public function chatConfig()
    {
        $chatDefaultConfigLists = ChatEnum::getDefaultChatConfig();
        $type = ConfigService::get('ai_chat',  'type');
        $openType = $type;
        if(in_array($type,$this->chatGLMModel)){
            $openType = ChatEnum::ZHIPUGLM;
        }
        //开启gpt的
        if(in_array($type,$this->gptModel)){
            $openType = $type;
        }
        if(in_array($type,$this->xinghuoModel)){
            $openType = ChatEnum::XINGHUO;
        }
        if(in_array($type,$this->wenxinModel)){
            $openType = ChatEnum::WENXIN;
        }
        //处理用户对话模型配置
        foreach ($chatDefaultConfigLists as $defaultConfig){
            $key = $defaultConfig['key'];
            switch ($key){
                //处理智普数据
                case ChatEnum::ZHIPUGLM:
                    //直接找chatGLM-turbo模型
                    $chatConfig = $chatConfigLists['chatGLM-Turbo'] ?? $defaultConfig;
                    $chatConfig = array_merge($defaultConfig,$chatConfig);
                    $chatConfig['key'] = $defaultConfig['key'];
                    $chatConfig['name'] = $defaultConfig['name'];
                    $chatConfig['model'] = $defaultConfig['model'];
                    ConfigService::set('chat_config',ChatEnum::ZHIPUGLM,$chatConfig);
                    Log::write('处理智谱数据:'.json_encode($chatConfig));
                    echo "更新智谱模型配置成功<br>";
                    $this->configCount++;
                    break;
                //处理科大讯飞
                case ChatEnum::XINGHUO:
                    ConfigService::set('chat_config',ChatEnum::XINGHUO,$defaultConfig);
                    Log::write('处理科大讯飞数据:'.json_encode($defaultConfig));
                    echo "更新科大讯飞模型配置成功<br>";
                    $this->configCount++;
                    break;
                //处理文心一言
                case ChatEnum::WENXIN:
                    $chatConfig = $chatConfigLists['BAIDU'] ?? $defaultConfig;
                    $chatConfig = array_merge($defaultConfig,$chatConfig);
                    $chatConfig['key'] = $defaultConfig['key'];
                    $chatConfig['name'] = $defaultConfig['name'];
                    $chatConfig['model'] = $defaultConfig['model'];
                    ConfigService::set('chat_config',ChatEnum::WENXIN,$chatConfig);
                    Log::write('处理文心一言数据:'.json_encode($chatConfig));
                    echo "更新文心一言模型配置成功<br>";
                    $this->configCount++;
                    break;
            }
        }
        ConfigService::set('chat_config','type',$openType);
        Log::write('新增chat_config.type配置，值为:'.$openType);
        echo "新增chat_config.type配置<br>";
        $this->configCount++;
    }


    /**
     * @notes 更新模型计费配置
     * @return void
     * @author cjhao
     * @date 2023/12/22 15:24
     */
    public function billingConfig()
    {
        //处理用户模型计费数据
        $billingConfigLists = ConfigService::get('chat_config','billing_config');
        $billingDefaultConfig = ChatEnum::getDefaultBillingConfig();
        $billingLists = [];
        foreach ($billingConfigLists as $configKey => $billingConfig){
            //处理智谱的
            if($configKey == 'chatGLM-Turbo'){
                $defaultConfig = $billingDefaultConfig[ZhiPuEnum::CHATGLM_TURBO] ?? [];
                $billingConfig['key'] = $defaultConfig['key'];
                $billingConfig['name'] = $defaultConfig['name'];
                $billingLists[ZhiPuEnum::CHATGLM_TURBO] = $billingConfig;
            }
            //处理gpt和api2d,看以前使用了哪些模型，直接开启对应的模型
            if(in_array($configKey,$this->gptModel)){
                if(in_array($configKey,['gpt3.5','gpt4.0'])){
                    $defaultConfig = ChatEnum::getDefaultChatConfig($configKey);
                    $chatConfig = ConfigService::get('chat_config',$configKey,$defaultConfig);
                    $keyModel = $chatConfig['model'];
                    $defaultConfig = ChatEnum::getDefaultBillingConfig($keyModel);
                    $billingConfig['key'] = $defaultConfig['key'];
                    $billingConfig['name'] = $defaultConfig['name'];
                    $billingConfig['alias'] = $keyModel;
                    $billingLists[$keyModel] = $billingConfig;
                }else{
                    $defaultConfig = ChatEnum::getDefaultChatConfig($configKey);
                    $chatConfig = ConfigService::get('chat_config',$configKey,$defaultConfig);
                    $keyModel = Api2dEnum::getAliasNameList($chatConfig['model']);
                    $defaultConfig = ChatEnum::getDefaultBillingConfig($keyModel);
                    $billingConfig['key'] = $defaultConfig['key'];
                    $billingConfig['name'] = $defaultConfig['name'];
                    $billingConfig['alias'] = $keyModel;
                    $billingLists[$keyModel] = $billingConfig;
                }


            }
            //处理科大讯飞
            if(in_array($configKey,$this->xinghuoModel)){
                switch ($configKey){
                    case 'XUNFEI':
                        $defaultConfig = ChatEnum::getDefaultBillingConfig(XingHuoEnum::XINGHUO15);
                        $billingConfig['key'] = $defaultConfig['key'];
                        $billingConfig['name'] = $defaultConfig['name'];
                        $billingLists[XingHuoEnum::XINGHUO15] = $billingConfig;
                        break;
                    case 'XUNFEI2.0':
                        $defaultConfig = ChatEnum::getDefaultBillingConfig(XingHuoEnum::XINGHUO20);
                        $billingConfig['key'] = $defaultConfig['key'];
                        $billingConfig['name'] = $defaultConfig['name'];
                        $billingLists[XingHuoEnum::XINGHUO20] = $billingConfig;
                        break;
                    case 'XUNFEI3.0':
                        $defaultConfig = ChatEnum::getDefaultBillingConfig(XingHuoEnum::XINGHUO30);
                        $billingConfig['key'] = $defaultConfig['key'];
                        $billingConfig['name'] = $defaultConfig['name'];
                        $billingLists[XingHuoEnum::XINGHUO30] = $billingConfig;
                        break;
                }
            }
            //处理文心一言
            if(in_array($configKey,$this->wenxinModel)){
                $defaultConfig = ChatEnum::getDefaultChatConfig(ChatEnum::WENXIN);
                $chatConfig = ConfigService::get('chat_config',$configKey,$defaultConfig);
                $keyModel = $chatConfig['model'];
                $defaultConfig = ChatEnum::getDefaultBillingConfig($keyModel);
                $billingConfig['key'] = $defaultConfig['key'];
                $billingConfig['name'] = $defaultConfig['name'];
                $billingConfig['alias'] = ChatEnum::getChatModelLists($keyModel);
                $billingLists[$keyModel] = $billingConfig;

            }
        }
        if($billingLists){
            ConfigService::set('chat_config','billing_config',$billingLists);
            Log::write('修改模型计费配置:'.json_encode($billingLists));
            $count = count($billingLists);
            echo "更新模型计费配置成功,共更新:$count 条<br>";
            $this->configCount++;
        }
    }

    /**
     * @notes 更新key池配置
     * @return void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author cjhao
     * @date 2023/12/22 15:32
     */
    public function keyPoolConfig()
    {
        //处理key池数据
        $keyPoolLists = KeyPool::where(['type'=>KeyPoolEnum::TYPE_CHAT,'ai_key'=> array_merge($this->chatGLMModel,$this->xinghuoModel,$this->wenxinModel)])
            ->select()
            ->toArray();
        $glmPoolIds = [];
        $xinghuoPoolIds = [];
        $wenxinPoolIds = [];
        foreach ($keyPoolLists as $keyPool){
            if(in_array($keyPool['ai_key'],$this->chatGLMModel)){
                $glmPoolIds[] = $keyPool['id'];
            }
            if(in_array($keyPool['ai_key'],$this->xinghuoModel)){
                $xinghuoPoolIds[] = $keyPool['id'];
            }
            if(in_array($keyPool['ai_key'],$this->wenxinModel)){
                $wenxinPoolIds[] = $keyPool['id'];
            }
        }
        if($glmPoolIds){
            KeyPool::where(['id'=>$glmPoolIds])->update(['ai_key' => ChatEnum::ZHIPUGLM]);
            $count = count($glmPoolIds);
            Log::write('更新智谱AI的key,共更新:'.$count.'条');
            echo "更新智谱AI的key成功,共更新: $count 条<br>";
            $this->configCount += $count;
        }
        if($xinghuoPoolIds){
            KeyPool::where(['id'=>$xinghuoPoolIds])->update(['ai_key' => ChatEnum::XINGHUO]);
            $count = count($xinghuoPoolIds);
            Log::write('更新星火大模型的key,共更新:'.$count.'条');
            echo "更新星火大模型的key成功,共更新: $count 条<br>";
            $this->configCount += $count;
        }
        if($wenxinPoolIds){
            KeyPool::where(['id'=>$wenxinPoolIds])->update(['ai_key' => ChatEnum::WENXIN]);
            $count = count($wenxinPoolIds);
            Log::write('更新文心一言的key,共更新:'.$count.'条');
            echo "更新文心一言的key成功,共更新: $count 条<br>";
            $this->configCount += $count;
        }
    }


    /**
     * @notes 更新对话记录的科大讯飞模型
     * @return void
     * @author cjhao
     * @date 2023/12/25 18:51
     */
    public function chatRecords()
    {
        //更新科大讯飞模型
        ChatRecords::where(['model'=>'XUNFEI'])->update(['model'=> XingHuoEnum::XINGHUO15]);
        ChatRecords::where(['model'=>'XUNFEI2.0'])->update(['model'=> XingHuoEnum::XINGHUO20]);
        ChatRecords::where(['model'=>'XUNFEI3.0'])->update(['model'=> XingHuoEnum::XINGHUO30]);
        echo "更新对话记录的科大讯飞模型成功<br>";
        $this->configCount++;
    }

    /**
     * @notes 更新绘画翻译
     * @return void
     * @author cjhao
     * @date 2023/12/25 18:55
     */
    public function drawTranslate()
    {
        $translateConfig  = ConfigService::get('draw_config','translate',DrawEnum::getTranslateConfig());
        $translateModel = $translateConfig['model'] ?? '';
        if($translateConfig && 'BAIDU'  == $translateModel){
            $translateConfig['model'] = ChatEnum::WENXIN;
            ConfigService::set('draw_config','translate',$translateConfig);
            echo "更新绘画翻译模型成功<br>";
            $this->configCount++;
        }
    }

    /**
     * @notes 更新key池下架规则
     * @return void
     * @author cjhao
     * @date 2023/12/27 9:55
     */
    public function chatDownKey()
    {
        $chatGlmRes = KeyDownRule::where(['type'=>KeyPoolEnum::TYPE_CHAT,'ai_key'=>$this->chatGLMModel])->update(['ai_key'=>ChatEnum::ZHIPUGLM]);
        $xingHuoRes = KeyDownRule::where(['type'=>KeyPoolEnum::TYPE_CHAT,'ai_key'=>$this->xinghuoModel])->update(['ai_key'=>ChatEnum::XINGHUO]);
        $wenxinRes = KeyDownRule::where(['type'=>KeyPoolEnum::TYPE_CHAT,'ai_key'=>$this->wenxinModel])->update(['ai_key'=>ChatEnum::WENXIN]);
        if($chatGlmRes || $xingHuoRes || $wenxinRes){
            echo "更新key自动下架规则成功<br>";
            $this->configCount++;
        }
    }
}
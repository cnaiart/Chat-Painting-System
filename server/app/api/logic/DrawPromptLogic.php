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


use app\common\enum\chat\ChatEnum;
use app\common\enum\DrawEnum;
use app\common\enum\YesNoEnum;
use app\common\logic\BaseLogic;
use app\common\model\draw\DrawCategory;
use app\common\model\draw\DrawPrompt;
use app\common\model\draw\DrawPromptExample;
use app\common\service\chat\ChatGptService;
use app\common\service\chat\WenXinService;
use app\common\service\ConfigService;


/**
 * 绘图关键词
 * Class DrawPromptLogic
 * @package app\api\logic
 */
class DrawPromptLogic extends BaseLogic
{
    /**
     * @notes 关键词列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 段誉
     * @date 2023/6/28 10:32
     */
    public static function category()
    {
        return DrawCategory::where(['status' => 1, 'pid' => 0])
            ->order(['sort' => 'desc', 'id' => 'desc'])
            ->hidden(['create_time', 'update_time', 'delete_time'])
            ->select()
            ->toArray();
    }

    /**
     * @notes 关键词
     * @param $cateId
     * @return DrawPrompt[]|array|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 段誉
     * @date 2023/6/28 11:12
     */
    public static function prompt($cateId)
    {
        $prompts = DrawPrompt::where(['status' => 1, 'category_id' => $cateId])
            ->hidden(['create_time', 'update_time', 'delete_time'])
            ->order(['sort' => 'desc', 'id' => 'desc'])
            ->select()->toArray();

        // 当前分类直属关键词
        $promptData = [];
        foreach ($prompts as $prompt) {
            $promptData[] = [
                'prompt' => $prompt['prompt'],
                'prompt_en' => $prompt['prompt_en'],
            ];
        }

        $catePromptData = [];

        $childCateData = DrawCategory::where(['status' => 1, 'pid' => $cateId])
            ->select()->toArray();

        foreach ($childCateData as $childCate) {
            $catePrompts = DrawPrompt::where(['status' => 1, 'category_id' => $childCate['id']])
                ->hidden(['create_time', 'update_time', 'delete_time'])
                ->order(['sort' => 'desc', 'id' => 'desc'])
                ->select()->toArray();

            $catePromptArr = [];
            foreach ($catePrompts as $catePrompt) {
                $catePromptArr[] = [
                    'prompt' => $catePrompt['prompt'],
                    'prompt_en' => $catePrompt['prompt_en'],
                ];
            }

            $catePromptData[] = [
                'cate_id' => $childCate['id'],
                'name' => $childCate['name'],
                'prompt' => $catePromptArr
            ];
        }

        return [
            'prompt' => $promptData,
            'cate_prompt' => $catePromptData,
        ];
    }

    /**
     * @notes 示例列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 段誉
     * @date 2023/7/17 16:31
     */
    public static function example()
    {
        $lists = DrawPromptExample::where(['status' => YesNoEnum::YES])
            ->hidden(['create_time', 'update_time', 'delete_time'])
            ->order(['sort' => 'desc', 'id' => 'desc'])
            ->select()->toArray();
         shuffle($lists);
         return array_slice($lists, 0, 2);
    }

    /**
     * @notes 翻译
     * @param $prompt
     * @return array|false|string
     * @author 段誉
     * @date 2023/7/18 9:43
     */
    public static function translate($prompt)
    {
        try {
            $translateRes = $prompt;

            // 绘画总开关
            $openConfig = ConfigService::get('draw_config', 'is_open', 0);
            if (!$openConfig) {
                return ['translate' => $translateRes];
            }

            // 自动翻译关键词
            $apiType = ConfigService::get('draw_config', 'type', '');
            if (empty($apiType)) {
                // 兼容旧版本
                $apiType = ConfigService::get('draw_config', 'api_type', DrawEnum::API_ZHISHUYUN_FAST);
            }

            // api配置
            $apiConfig = ConfigService::get('draw_config', $apiType, []);
            if (isset($apiConfig['auto_translate']) && $apiConfig['auto_translate'] == 1
                && isset($apiConfig['translate_type']) && $apiConfig['translate_type'] == 2 ) {
                // 翻译配置
                $translateConfig = ConfigService::get('draw_config', 'translate', DrawEnum::getTranslateConfig());
                $model = $translateConfig['model'] ?? ChatEnum::OPEN_GPT_35;
                if($model == ChatEnum::WENXIN){
                    $chatService = (new WenXinService());
                }else{
                    $chatService = (new ChatGptService());
                }
                // 自动翻译
                $prompt = str_replace('{prompt}', $prompt, $translateConfig['prompt']);
                $translateRes = $chatService->translate($prompt);
            }

            return ['translate' => $translateRes];

        } catch (\Exception $e) {
            self::$error = $e->getMessage();
            return false;
        }
    }

}
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

use app\common\cache\DrawYjSelectorCache;
use app\common\enum\ContentCensorEnum;
use app\common\enum\chat\ChatEnum;
use app\common\enum\DefaultEnum;
use app\common\enum\DrawEnum;
use app\common\enum\user\AccountLogEnum;
use app\common\enum\YesNoEnum;
use app\common\logic\AccountLogLogic;
use app\common\logic\BaseLogic;
use app\common\model\draw\DrawRecords;
use app\common\model\user\User;
use app\common\service\chat\AiChatService;
use app\common\service\chat\ChatGptService;
use app\common\service\chat\WenXinService;
use app\common\service\ConfigService;
use app\common\service\draw\DrawDriver;
use app\common\service\draw\engine\DrawSd;
use app\common\service\FileService;
use app\common\service\QueueService;
use app\common\service\storage\Driver as StorageDriver;
use think\Exception;
use think\facade\Db;
use think\facade\Log;

/**
 * 绘图逻辑
 * Class DrawLogic
 * @package app\api\logic
 */
class DrawLogic extends BaseLogic
{
    /**
     * @notes 生成图片
     * @param $userId
     * @param $params
     * @return array|false
     * @author 段誉
     * @date 2023/7/24 15:17
     */
    public static function imagine($userId, $params)
    {
        //校验能否绘画
        $checkResult = self::checkAbleDraw($userId, $params);
        if ($checkResult !== true) {
            self::$error = $checkResult;
            return false;
        }

        $params['prompt'] = trim($params['prompt'], ',');

        // 提示词校验
        if (false === self::promptCensor($params['prompt'])) {
            return false;
        }

        // 翻译提示词
        $translateResult = self::autoTranslatePrompt($params['prompt']);
        if ($translateResult === false) {
            return false;
        }
        $params['prompt_en'] = $translateResult;

        // 写入绘画记录，扣除用户余额 (内含事务)
        $recordData = self::drawRecordHandle($userId, $params);
        if ($recordData === false) {
            return false;
        }

        // 发起绘图请求
        $drawRes = self::drawImagineHandle($recordData['record'], $recordData['image_id']);
        if (false === $drawRes) {
            return false;
        }

        return ['records_id' => $recordData['record']['id']];
    }

    /**
     * @notes 校验能否绘画
     * @param $userId
     * @param $params
     * @return bool|string
     * @author mjf
     * @date 2024/1/26 18:38
     */
    public static function checkAbleDraw($userId, $params): bool|string
    {
        // 用户已拉黑不允许绘画
        $user = User::where('id', $userId)->findOrEmpty()->toArray();
        if (empty($user)) {
            return '非法会员';
        }
        if(YesNoEnum::YES == $user['is_blacklist']) {
            return '您已被管理员禁止生成，请联系客服详询原因。';
        }

        $openConfig = ConfigService::get('draw_config', 'is_open', 0);;
        if ($openConfig != 1) {
            return "绘画功能已关闭";
        }

        $drawModel = $params['model'] ?? '';
        $drawBillConfig = self::modelConfig($userId, $drawModel);
        if(empty($drawBillConfig) || $drawModel != $drawBillConfig['model']) {
            return '当前模型不支持使用';
        }

        // 会员
        if ($drawBillConfig['member_free']) {
            return true;
        }

        // 非会员
        $needBalance = $drawBillConfig['balance'] ?? 1;
        if ($user['balance_draw'] < $needBalance) {
            return '余额不足';
        }
        return true;
    }

    /**
     * @notes 翻译提示词
     * @param $prompt
     * @return array|false|string
     * @author 段誉
     * @date 2023/7/7 15:50
     */
    public static function autoTranslatePrompt($prompt)
    {
        try {
            $promptEn = "";
            // 自动翻译关键词
            $apiType = ConfigService::get('draw_config', 'type', DrawEnum::API_ZHISHUYUN_FAST);
            // api配置
            $apiConfig = ConfigService::get('draw_config', $apiType, []);
            if (isset($apiConfig['auto_translate']) && $apiConfig['auto_translate'] == 1
                && isset($apiConfig['translate_type']) && $apiConfig['translate_type'] == 1) {
                // 翻译配置
                $translateConfig = ConfigService::get('draw_config', 'translate', DrawEnum::getTranslateConfig());
                // 自动翻译
                $model = $translateConfig['model'] ?? ChatEnum::OPEN_GPT_35;
                if($model == ChatEnum::WENXIN){
                    $chatService = (new WenXinService());
                }else{
                    $chatService = (new ChatGptService());
                }
                // 自动翻译
                $prompt = str_replace('{prompt}', $prompt, $translateConfig['prompt']);
                $promptEn = $chatService->translate($prompt);
            }
            return $promptEn;
        } catch (\Exception $e) {
            self::$error = "翻译失败:" . $e->getMessage();
            return false;
        }
    }

    /**
     * @notes 绘画记录处理
     * @param $userId
     * @param $params
     * @return array|false
     * @author 段誉
     * @date 2023/6/25 16:10\
     */
    public static function drawRecordHandle($userId, $params)
    {
        Db::startTrans();
        try {
            // 绘画类型
            $drawType = DrawEnum::TYPE_TEXT_TO_IMAGE;
            // 绘画动作
            $action = $params['action'];
            // 操作图片id
            $imageId = '';
            // 生图时不想出现的内容
            $noContent = '';
            // 绘画版本
            $version = '';
            // 绘画风格
            $style = 'default';
            // 绘画引擎
            $engine = 'midjourney';
            // 质量
            $quality = '';

            // 关键词
            $prompt = $params['prompt'];
            if (!empty($params['prompt_en'])) {
                $prompt = $params['prompt_en'];
            }

            // uv操作
            if ($params['action'] != DrawEnum::ACTION_GENERATE) {
                if (str_contains($params['action'], DrawEnum::ACTION_UPSAMPLE)) {
                    $drawType = DrawEnum::TYPE_UPSCALE_IMAGE;
                }

                if (str_contains($params['action'], DrawEnum::ACTION_VARIATION)) {
                    $drawType = DrawEnum::TYPE_VARIATION_IMAGE;
                }

                $imageId = $params['image_id'];
            }

            // 垫图
            if (!empty($params['image_base'])) {
                $drawType = DrawEnum::TYPE_IMAGE_TO_IMAGE;
                if (!in_array($params['model'], [DrawEnum::API_SD ,DrawEnum::API_YIJIAN_SD, DrawEnum::API_DALLE3])) {
                    $prompt = $params['image_base'] . ' ' . $prompt;
                }
            }

            // 意间sd
            if ($params['model'] == DrawEnum::API_YIJIAN_SD) {
                // 绘画引擎
                $engine = !empty($params['engine']) ? $params['engine'] : 'stable_diffusion';
                // 风格
                $style = !empty($params['style']) ? $params['style'] : '';
            }

            // dalle3
            if ($params['model'] == DrawEnum::API_DALLE3) {
                // 绘画引擎
                $engine = 'dall-e-3';
                // 风格
                $style = !empty($params['style']) ? $params['style'] : 'vivid';
                // 质量
                $quality = !empty($params['quality']) ? $params['quality'] : 'standard';
            }

            // 本地sd
            if ($params['model'] == DrawEnum::API_SD) {
                $engine = !empty($params['engine']) ? $params['engine'] : '';
            }

            // 非意间，dalle3,sd
            if (!in_array($params['model'], [DrawEnum::API_SD, DrawEnum::API_YIJIAN_SD, DrawEnum::API_DALLE3])) {
                // 非意间sd
                // 图片比例
                if (!empty($params['scale'])) {
                    $prompt .= ' --ar ' . $params['scale'];
                }

                // 其他参数
                if (!empty($params['other'])) {
                    $prompt .= ' ' . $params['other'];
                }

                // 绘画中不想出现的内容
                if (!empty($params['no_content'])) {
                    $prompt .= ' --no ' . $params['no_content'];
                    $noContent = $params['no_content'];
                }

                // 版本参数
                if (!empty($params['version'])) {
                    if (str_contains($params['version'], '--v') || str_contains($params['version'], '--niji')) {
                        $prompt .= ' ' . $params['version'];
                        $version = $params['version'];
                        if (!empty($params['style'])
                            && $params['style'] != 'default'
                            && $version == DrawEnum::MJ_VERSION_NIJI5) {
                            $style = $params['style'];
                            $prompt .= ' --style ' . $params['style'];
                        }
                    }
                }
            }

            // 如果开启模型计费则使用
            $drawModel = self::modelConfig($userId, $params['model']);

            if (false == $drawModel['member_free']) {
                // 扣除用户余额
                self::drawBalanceHandle(
                    $userId,
                    $drawModel['balance'],
                    AccountLogEnum::DRAW_DEC_IMAGE
                );
            }

            self::drawTotalHandle($userId, AccountLogEnum::DRAW_DEC_IMAGE);

            // 增加绘图记录
            $record = DrawRecords::create([
                'user_id' => $userId,
                'type' => $drawType,
                'action' => $action,
                'prompt' => $params['prompt'],
                'prompt_en' => $params['prompt_en'],
                'prompt_desc' => $prompt,
                'prompt_other' => $params['other'] ?? '',
                'scale' => $params['scale'] ?? '',
                'image_base' => $params['image_base'] ?? '',
                'status' => DrawEnum::STATUS_NOT,
                'use_tokens' => $drawModel['balance'],
                'model' => $drawModel['model'],
                'version' => $version,
                'no_content' => $noContent,
                'style' => $style,
                'engine' => $engine,
                'quality' => $quality,
            ]);

            Db::commit();

            return [
                'record' => $record->toArray(),
                'image_id' => $imageId,
            ];

        } catch (\Exception $e) {
            Db::rollback();
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * @notes 绘画生成处理
     * @param $record
     * @param $selectImageId
     * @return bool
     * @author 段誉
     * @date 2023/7/25 16:57
     */
    public static function drawImagineHandle($record, $selectImageId)
    {
        $response = [];
        try {
            // 更新绘图记录
            switch ($record['model']) {
                // 知数云
                case DrawEnum::API_ZHISHUYUN_FAST:
                case DrawEnum::API_ZHISHUYUN_RELAX:
                case DrawEnum::API_ZHISHUYUN_TURBO:
                    // 投递任务 关键词生图
                    $drawDriver = new DrawDriver($record['model']);
                    $response = $drawDriver->imagine([
                        'prompt' => $record['prompt_desc'],
                        'action' => $record['action'],
                        'image_id' => $selectImageId,
                    ]);

                    DrawRecords::where(['id' => $record['id']])->update([
                        'task_id' => $response['task_id'] ?? '',
                        'notify_snap' => json_encode($response, JSON_UNESCAPED_UNICODE),
                        'status' => DrawEnum::STATUS_IN_PROGRESS,
                        'update_time' => time(),
                    ]);
                    // 设置key使用日志
                    $drawDriver->setKeyLog($record['id']);
                    break;
                // 官方直连-MJ
                case DrawEnum::API_MDDAI_MJ:
                    $drawDriver = new DrawDriver($record['model']);
                    $actionData = DrawEnum::getActionAndIndex($record['action']);
                    if ($actionData['action'] == DrawEnum::ACTION_GENERATE) {
                        $imageBase = $record['image_base'] ?? '';
                        $imageBase = !empty($imageBase) ? FileService::getFileUrl($imageBase) : '';
                        $response = $drawDriver->imagine([
                            'prompt' => $record['prompt_desc'],
                            'image_base' => $imageBase,
                        ]);
                    } else {
                        $response = $drawDriver->imagineUv([
                            'action' => $actionData['action'],
                            'index' => $actionData['index'],
                            'task_id' => $selectImageId,
                        ]);
                    }

                    DrawRecords::where(['id' => $record['id']])->update([
                        'task_id' => $response['task_id'] ?? '',
                        'notify_snap' => json_encode($response, JSON_UNESCAPED_UNICODE),
                        'status' => DrawEnum::STATUS_IN_PROGRESS,
                        'update_time' => time(),
                    ]);

                    // 设置key使用日志
                    $drawDriver->setKeyLog($record['id']);
                    break;
                // 意间-SD
                case DrawEnum::API_YIJIAN_SD:
                    // 加入意间绘画队列
                    $record['notify_domain'] = request()->domain();

                    // 更新状态为执行中
                    DrawRecords::where(['id' => $record['id']])->update([
                        'status' => DrawEnum::STATUS_IN_PROGRESS,
                        'update_time' => time(),
                    ]);

                    // 提交到队列
                    QueueService::pushYj($record);
                    break;
                // Dalle-3
                case DrawEnum::API_DALLE3:
                    $record['file_domain'] = FileService::getFileUrl();
                    // 更新状态为执行中
                    DrawRecords::where(['id' => $record['id']])->update([
                        'status' => DrawEnum::STATUS_IN_PROGRESS,
                        'update_time' => time(),
                    ]);
                    // 提交到队列
                    QueueService::pushDalle($record);
                    break;
                // 本地SD
                case DrawEnum::API_SD:
                    $record['file_domain'] = FileService::getFileUrl();
                    // 更新状态为执行中
                    DrawRecords::where(['id' => $record['id']])->update([
                        'status' => DrawEnum::STATUS_IN_PROGRESS,
                        'update_time' => time(),
                    ]);
                    // 提交队列
                    QueueService::pushSd($record);
                    break;
            }

            return true;

        } catch (\Exception $e) {
            self::$error = $e->getMessage();

            // 生成图片失败更新记录状态
            DrawRecords::where(['id' => $record['id']])->update([
                'status' => DrawEnum::STATUS_FAIL,
                'notify_snap' => !empty($response) ? json_encode($response, JSON_UNESCAPED_UNICODE) : [],
                'fail_reason' => $e->getMessage(),
                'update_time' => time(),
            ]);

            // 生成失败, 没有任务id,回退用户金额
            self::drawBalanceHandle(
                $record['user_id'], $record['use_tokens'],
                AccountLogEnum::DRAW_INC_DRAW_FAIL
            );

            self::drawTotalHandle($record['user_id'], AccountLogEnum::DRAW_INC_DRAW_FAIL);

            return false;
        }
    }

    /**
     * @notes 绘画计费模型
     * @return array
     * @author 段誉
     * @date 2023/7/19 11:59
     */
    public static function drawBillingConfig()
    {
        $isOpen = ConfigService::get('draw_config', 'billing_is_open', 0);
        $billingConfig = ConfigService::get('draw_config', 'billing_config', []);
        $defaultConfigLists = DrawEnum::getDefaultBillingConfig();

        $billingModelList = [];
        foreach ($billingConfig as $key => $config) {
            $defaultConfig = $defaultConfigLists[$key] ?? [];
            unset($defaultConfigLists[$key]);
            $billingConfig = array_merge($defaultConfig, $config);
            $billingModelList[$key] = $billingConfig;
        }

        $billingModelList = array_merge($billingModelList, $defaultConfigLists);

        return [
            'is_open' => $isOpen,
            'billing_config' => $billingModelList,
        ];
    }

    /**
     * @notes 模型配置
     * @param $userId
     * @param bool $drawModel
     * @return array
     * @author mjf
     * @date 2024/1/26 18:01
     */
    public static function modelConfig($userId, $drawModel = true): array
    {
        $modelArr = [];
        $config = self::drawBillingConfig();
        $apiType = ConfigService::get('draw_config', 'type', DrawEnum::API_ZHISHUYUN_FAST);
        $default = DrawEnum::getDefaultBillingConfig($apiType);

        $balance = 1;
        $memberFree = false;
        $memberStatus = self::userIsMember($userId);
        if ($memberStatus) {
            $balance = 0;
            $memberFree = true;
        }

        if ($config['is_open'] == 0) {
            $modelArr[$default['key']] = [
                'name' => !empty($default['alias']) ? $default['alias'] : $default['name'],
                'model' => $default['key'],
                'balance' => $balance,
                'default' => true,
                'member_free' => $memberFree,
            ];

            if ($drawModel === true) {
                return array_values($modelArr);
            }

            return $modelArr[$default['key']];
        }

        $defaultStatus = false;
        $config['billing_config'] = array_values($config['billing_config']);

        foreach ($config['billing_config'] as $item) {
            if (0 == $item['status']) {
                continue;
            }

            $itemDefaultStatus = false;
            if ($apiType == $item['key']) {
                $defaultStatus = true;
                $itemDefaultStatus = true;
            }

            $balance = (int)$item['balance'];
            // 会员不消耗次数
            $memberFree = false;
            if ($memberStatus && !empty($item['member_free'])) {
                $balance = 0;
                $memberFree = true;
            }

            $model = [
                'name' => !empty($item['alias']) ? $item['alias'] : $item['name'],
                'model' => $item['key'],
                'balance' => $balance,
                'default' => $itemDefaultStatus,
                'member_free' => $memberFree,
            ];

            if ($model['name'] == '码多多-MJ') {
                $model['name'] = '官方直连-MJ';
            }

            if ($model['name'] == '知数云-MJ') {
                $model['name'] = '知数云-快速MJ';
            }

            $modelArr[$item['key']] = $model;
        }

        if (!empty($modelArr) && false === $defaultStatus) {
            $modelArr[array_keys($modelArr)[0]]['default'] = true;
        }

        if(true === $drawModel) {
            return array_values($modelArr);
        }
        return $modelArr[$drawModel] ?? [];
    }

    /**
     * @notes 绘画余额处理
     * @param $userId
     * @param $usedToken
     * @param $changeType
     * @author 段誉
     * @date 2023/7/24 11:35
     */
    public static function drawBalanceHandle($userId, $usedToken, $changeType)
    {
        if ($usedToken <= 0) {
            return;
        }

        // 用户信息
        $user = User::findOrEmpty($userId);

        // $action 变动类型 $balanceDraw 绘画余额
        if (in_array($changeType, AccountLogEnum::DRAW_INC)) {
            $action = AccountLogEnum::INC;
            $balanceDraw = $user->balance_draw + $usedToken;
        } else {
            $action = AccountLogEnum::DEC;
            $balanceDraw = $user->balance_draw - $usedToken;
        }

        $user->balance_draw = $balanceDraw;
        $user->save();

        // 记录账户流水
        AccountLogLogic::add($userId, $changeType, $action, $usedToken);
    }

    /**
     * @notes 累计绘画次数处理
     * @param $userId
     * @param $changeType
     * @author mjf
     * @date 2024/2/3 11:53
     */
    public static function drawTotalHandle($userId, $changeType)
    {
        // 用户信息
        $user = User::findOrEmpty($userId);

        // $totalDraw 累计绘画消费
        if (in_array($changeType, AccountLogEnum::DRAW_INC)) {
            $totalDraw = $user->total_draw - 1;
        } else {
            $totalDraw = $user->total_draw + 1;
        }
        $totalDraw = $totalDraw < 0 ? 0 : $totalDraw;

        $user->total_draw = $totalDraw;
        $user->save();
    }

    /**
     * @notes 绘画回调处理
     * @param $response
     * @return false|void
     * @author 段誉
     * @date 2023/6/25 14:52
     */
    public static function notifyZsy($response)
    {
        try {
            if (!isset($response['success']) || empty($response['task_id'])) {
                throw new \Exception("回调参数缺失");
            }

            // 绘图记录
            $record = DrawRecords::where(['task_id' => $response['task_id']])->findOrEmpty();
            if ($record->isEmpty()) {
                throw new \Exception("绘图记录信息不存在");
            }

            // 已标记成功或失败的记录不处理
            if (in_array($record['status'], [DrawEnum::STATUS_FAIL, DrawEnum::STATUS_SUCCESS])) {
                throw new \Exception("绘图记录状态为已成功或失败，无需处理");
            }

            // 回调快照
            $oldNotifySnap = !empty($record['notify_snap']) ? $record['notify_snap'] : '';
            $nowNotifySnap = json_encode($response, JSON_UNESCAPED_UNICODE);
            $notifySnap = trim($oldNotifySnap . ',' . $nowNotifySnap, ',');

            // 更新信息
            $updateData = [
                'status' => DrawEnum::STATUS_FAIL,
                'notify_snap' => $notifySnap,
                'able_actions' => !empty($response['actions']) ? json_encode($response['actions']) : "",
                'fail_reason' => !empty($response['detail']) ? $response['detail'] : '',
                'update_time' => time(),
            ];

            // 回调成功
            if ($response['success'] == true) {
                // 成功
                $updateData['status'] = DrawEnum::STATUS_SUCCESS;

                // 下载到本地
                if (!empty($response['image_url'])) {
                    $image = self::downloadImage($response['image_url']);
                    $thumbnail = self::getThumbnail($response['image_url']);
                    $updateData['image'] = $image;
                    $updateData['thumbnail'] = !empty($thumbnail) ? $thumbnail : '';
                    $updateData['image_id'] = $response['image_id'] ?? '';
                    $updateData['image_url'] = $response['image_url'] ?? '';
                }
            } else {
                self::drawBalanceHandle(
                    $record['user_id'], $record['use_tokens'],
                    AccountLogEnum::DRAW_INC_DRAW_FAIL
                );
                self::drawTotalHandle($record['user_id'], AccountLogEnum::DRAW_INC_DRAW_FAIL);
            }

            DrawRecords::where(['id' => $record['id']])->update($updateData);

            // 图片审核
            self::imageCensor($record['id']);

            return true;
        } catch (\Exception $e) {
            Log::write('知数云绘图回调失败:' . $e->getMessage() . $e->getLine());
            if (!empty($response)) {
                Log::write('知数云绘图回调失败:' . json_encode($response, JSON_UNESCAPED_UNICODE));
            }
            return false;
        }
    }

    /**
     * @notes 直连回调处理
     * @param $response
     * @author 段誉
     * @date 2023/8/3 16:01
     */
    public static function notifyMdd($response)
    {
        try {
            if (!isset($response['code']) || !isset($response['data'])) {
                throw new \Exception("回调参数缺失");
            }

            $responseData = $response['data'];

            // 绘图记录
            $record = DrawRecords::where(['task_id' => $responseData['task_id']])->findOrEmpty();
            if ($record->isEmpty()) {
                throw new \Exception("绘图记录信息不存在");
            }

            // 已标记成功或失败的记录不处理
            if (in_array($record['status'], [DrawEnum::STATUS_FAIL, DrawEnum::STATUS_SUCCESS])) {
                throw new \Exception("绘图记录状态为已成功或失败，无需处理");
            }

            // 回调快照
            $oldNotifySnap = !empty($record['notify_snap']) ? $record['notify_snap'] : '';
            $nowNotifySnap = json_encode($responseData, JSON_UNESCAPED_UNICODE);
            $notifySnap = trim($oldNotifySnap . ',' . $nowNotifySnap, ',');

            $ableAction = [];
            $finalAction = DrawEnum::getActionAndIndex($record['action']);
            if (in_array($finalAction['action'], [DrawEnum::ACTION_GENERATE, DrawEnum::ACTION_VARIATION])) {
                $ableAction = [
                    "upsample1", "upsample2", "upsample3", "upsample4",
                    "variation1", "variation2", "variation3", "variation4"
                ];
            }

            // 更新信息
            $updateData = [
                'status' => DrawEnum::STATUS_FAIL,
                'notify_snap' => $notifySnap,
                'able_actions' => !empty($responseData['actions']) ? json_encode($responseData['actions']) : json_encode($ableAction),
                'fail_reason' => !empty($response['msg']) ? $response['msg'] : '',
                'update_time' => time(),
            ];

            // 回调成功
            if ($response['code'] == 1) {
                // 成功
                $updateData['status'] = DrawEnum::STATUS_SUCCESS;

                // 下载到本地
                if (!empty($responseData['image_url'])) {
                    $proxyUrl = $responseData['image_url'];
                    $apiConfig = ConfigService::get('draw_config', DrawEnum::API_MDDAI_MJ, []);
                    if (!empty($apiConfig['proxy_url'])) {
                        $proxyUrl = str_replace("https://cdn.discordapp.com", $apiConfig['proxy_url'], $responseData['image_url']);
                    }

                    $image = self::downloadImage($proxyUrl);
                    $thumbnail = self::getThumbnail($proxyUrl);

                    $updateData['thumbnail'] = !empty($thumbnail) ? $thumbnail : '';
                    $updateData['image'] = $image;
                    $updateData['image_id'] = $responseData['task_id'] ?? '';
                    $updateData['image_url'] = $responseData['image_url'] ?? '';
                }
            } else {
                self::drawBalanceHandle(
                    $record['user_id'], $record['use_tokens'],
                    AccountLogEnum::DRAW_INC_DRAW_FAIL
                );
                self::drawTotalHandle($record['user_id'], AccountLogEnum::DRAW_INC_DRAW_FAIL);
            }

            DrawRecords::where(['id' => $record['id']])->update($updateData);

            // 图片审核
            self::imageCensor($record['id']);

            return true;

        } catch (\Exception $e) {
            Log::write('官方直连-MJ绘图回调失败:' . $e->getMessage() . $e->getLine());
            if (!empty($response)) {
                Log::write('官方直连-MJ绘图回调失败:' . json_encode($response, JSON_UNESCAPED_UNICODE));
            }

            return false;
        }
    }

    /**
     * @notes 意间回调
     * @param $response
     * @author mjf
     * @date 2023/11/6 17:17
     */
    public static function notifyYj($response)
    {
        try {
            if (!isset($response['task'])) {
                throw new \Exception("回调参数缺失");
            }

            $response = json_decode($response['task'], JSON_UNESCAPED_UNICODE);

            // 绘图记录
            $record = DrawRecords::where(['task_id' => $response['Uuid'] ?? ''])->findOrEmpty();
            if ($record->isEmpty()) {
                throw new \Exception("绘图记录信息不存在");
            }

            // 已标记成功或失败的记录不处理
            if (in_array($record['status'], [DrawEnum::STATUS_FAIL, DrawEnum::STATUS_SUCCESS])) {
                throw new \Exception("绘图记录状态为已成功或失败，无需处理");
            }

            // 地址为空时默认为失败
            if (empty($response['ImagePath'])) {
                // 更新
                $updateData = [
                    'notify_snap' => json_encode($response,JSON_UNESCAPED_UNICODE),
                    'status' => DrawEnum::STATUS_FAIL,
                    'update_time' => time(),
                ];
                DrawRecords::where(['id' => $record['id']])->update($updateData);

                self::drawBalanceHandle(
                    $record['user_id'], $record['use_tokens'],
                    AccountLogEnum::DRAW_INC_DRAW_FAIL
                );

                self::drawTotalHandle($record['user_id'], AccountLogEnum::DRAW_INC_DRAW_FAIL);

            } else {
                // 下载原图
                $image = self::downloadImage($response['ImagePath']);
                // 下载缩略图
                $thumbnail = self::getThumbnail($response['ImagePath']);
                if (empty($thumbnail) && !empty($response['ThumbImagePath'])) {
                    $thumbnail = self::downloadImage($response['ThumbImagePath'], 'uploads/thumbnail/');
                }
                // 更新
                $updateData = [
                    'notify_snap' => json_encode($response,JSON_UNESCAPED_UNICODE),
                    'update_time' => time(),
                    'status' => DrawEnum::STATUS_SUCCESS,
                    'image_url' => $image,
                    'image' => $image,
                    'thumbnail' => $thumbnail,
                ];
                DrawRecords::where(['id' => $record['id']])->update($updateData);

                // 图片审核
                self::imageCensor($record['id']);
            }

        } catch (\Exception $e) {
            Log::write('意间绘图回调失败:' . $e->getMessage() . $e->getLine());
            if (!empty($response)) {
                Log::write('意间绘图回调失败:' . json_encode($response, JSON_UNESCAPED_UNICODE));
            }
        }
    }

    /**
     * @notes Dalle3处理
     * @param $task
     * @param $response
     * @return bool
     * @throws \think\Exception
     * @author mjf
     * @date 2023/12/6 14:27
     */
    public static function notifyDalle3($task, $response)
    {
        if (empty($response['data'][0]['b64_json'])) {
            throw new \Exception('参数异常');
        }

        $base64 = base64_decode($response['data'][0]['b64_json']);
        $fileName = md5($base64) . '.png';
        // 下载原图，下载缩略图 考虑oss
        $baseDir = app()->getRootPath() . 'public/';
        $saveDir = 'uploads/draw/' . date('Ymd') . '/';
        $filePath = $saveDir . $fileName;

        if (!file_exists($baseDir . $saveDir)) {
            mkdir($baseDir . $saveDir, 0755, true);
        }
        // 保存图片文件到本地
        $localPath = $baseDir . $saveDir . $fileName;
        file_put_contents($localPath, $base64);

        // 非本地储存时上传图片到第三方
        $config = [
            'default' => ConfigService::get('storage', 'default', 'local'),
            'engine' => ConfigService::get('storage')
        ];
        if ($config['default'] != 'local') {
            // 第三方存储
            $StorageDriver = new StorageDriver($config);
            if (!$StorageDriver->fetch($localPath, $filePath)) {
                throw new \Exception('Dalle-3绘图保存失败:' . $StorageDriver->getError());
            }
        }

        // 缩略图
        $thumbnail = DrawLogic::getThumbnail($localPath);

        if ($config['default'] != 'local') {
            unlink($localPath);
        }

        // 更新记录
        DrawRecords::update([
            'id' => $task['id'],
            'status' => DrawEnum::STATUS_SUCCESS,
            'image' => $filePath,
            'image_url' => $filePath,
            'thumbnail' => $thumbnail,
        ]);

        // 图片审核
        self::imageCensor($task['id'], $task['file_domain']);

        return true;
    }

    /**
     * @notes 本地sd处理
     * @param $task
     * @param $response
     * @return bool
     * @throws Exception
     * @author mjf
     * @date 2024/1/4 14:39
     */
    public static function notifySd($task, $response)
    {
        if (empty($response['images'][0])) {
            throw new \Exception('参数异常');
        }

        $base64 = base64_decode($response['images'][0]);
        $fileName = md5($base64) . '.png';
        // 下载原图，下载缩略图 考虑oss
        $baseDir = app()->getRootPath() . 'public/';
        $saveDir = 'uploads/draw/' . date('Ymd') . '/';
        $filePath = $saveDir . $fileName;

        if (!file_exists($baseDir . $saveDir)) {
            mkdir($baseDir . $saveDir, 0755, true);
        }
        // 保存图片文件到本地
        $localPath = $baseDir . $saveDir . $fileName;
        file_put_contents($localPath, $base64);

        // 非本地储存时上传图片到第三方
        $config = [
            'default' => ConfigService::get('storage', 'default', 'local'),
            'engine' => ConfigService::get('storage')
        ];
        if ($config['default'] != 'local') {
            // 第三方存储
            $StorageDriver = new StorageDriver($config);
            if (!$StorageDriver->fetch($localPath, $filePath)) {
                throw new \Exception('SD绘图保存失败:' . $StorageDriver->getError());
            }
        }

        // 缩略图
        $thumbnail = DrawLogic::getThumbnail($localPath);

        if ($config['default'] != 'local') {
            unlink($localPath);
        }

        // 更新记录
        DrawRecords::update([
            'id' => $task['id'],
            'status' => DrawEnum::STATUS_SUCCESS,
            'image' => $filePath,
            'image_url' => $filePath,
            'thumbnail' => $thumbnail,
        ]);

        // 图片审核
        self::imageCensor($task['id'], $task['file_domain'] ?? '');

        return true;
    }

    /**
     * @notes 下载图片
     * @param $downloadUrl
     * @return string
     * @author 段誉
     * @date 2023/8/3 16:21
     */
    public static function downloadImage($downloadUrl, $saveDir = 'uploads/draw/'): string
    {
        if (empty($downloadUrl)) {
            return '';
        }

        try {
            // 存储引擎
            $config = [
                'default' => ConfigService::get('storage', 'default', 'local'),
                'engine' => ConfigService::get('storage')
            ];

            // 文件名称
            $fileName = md5($downloadUrl) . '.png';

            // 第三方存储
            if ($config['default'] == 'local') {
                $downSaveDir = app()->getRootPath() . 'public/' . $saveDir;
                download_file($downloadUrl, $downSaveDir . date('Ymd') . '/', $fileName, false);
                return $saveDir . date('Ymd') . '/' . $fileName;
            } else {
                $localPath = $saveDir . date('Ymd') . '/' . $fileName;
                $StorageDriver = new StorageDriver($config);
                if (!$StorageDriver->fetch($downloadUrl, $localPath)) {
                    throw new \Exception('绘图保存失败:' . $StorageDriver->getError());
                }
                return $localPath;
            }

        } catch (\Exception $e) {
            Log::write('绘图回调下载:' . $e->getMessage() . $e->getLine());
            return "";
        }
    }

    /**
     * @notes 生成缩略图
     * @param $originalImagePath
     * @return string
     * @author 段誉
     * @date 2023/8/4 17:00
     */
    public static function getThumbnail($originalImagePath)
    {
        try {
            // 保存路径
            $saveDir = 'uploads/thumbnail/' . date('Ymd') . '/';
            $fileName = self::imageUrlTrim(basename($originalImagePath), true);

            // 缩略图保存路径
            $thumbnailImagePath = app()->getRootPath() . 'public/' . $saveDir;

            if (!is_dir($thumbnailImagePath)) {
                mkdir($thumbnailImagePath, 0755, true);
            }

            // 缩略图的宽度和高度
            $thumbnailWidth = 450;

            // 创建原始图像资源
            $info = getimagesize($originalImagePath);

            $fn = $info['mime'];//获得图片类型；
            $originalImage = match ($fn) {
                'image/jpeg' => imagecreatefromjpeg($originalImagePath),
                'image/png' => imagecreatefrompng($originalImagePath),
                'image/webp' => imagecreatefromwebp($originalImagePath),
            };

            // 获取原始图像的宽度和高度
            $originalWidth = imagesx($originalImage);
            $originalHeight = imagesy($originalImage);

            // 计算缩略图的宽度和高度
            $thumbnailHeight = intval($originalHeight * $thumbnailWidth / $originalWidth);

            // 创建缩略图资源
            $thumbnailImage = imagecreatetruecolor($thumbnailWidth, $thumbnailHeight);

            // 将原始图像复制到缩略图中，并进行缩放
            imagecopyresampled($thumbnailImage, $originalImage, 0, 0, 0, 0, $thumbnailWidth, $thumbnailHeight, $originalWidth, $originalHeight);

            // 保存缩略图到文件
            imagepng($thumbnailImage, $thumbnailImagePath . $fileName);

            // 释放资源
            imagedestroy($originalImage);
            imagedestroy($thumbnailImage);

            // 第三方存储的情况
            $config = [
                'default' => ConfigService::get('storage', 'default', 'local'),
                'engine' => ConfigService::get('storage')
            ];

            if ($config['default'] != 'local') {
                // 第三方存储
                $filePath = $saveDir . $fileName;
                $localPath = $thumbnailImagePath . $fileName;
                $StorageDriver = new StorageDriver($config);
                if (!$StorageDriver->fetch($localPath, $filePath)) {
                    throw new \Exception('绘图缩略图保存失败:' . $StorageDriver->getError());
                }
            }

            return $saveDir . $fileName;
        } catch (\Exception $e) {
            Log::write('缩略图生成失败:' . $e->getMessage() . $e->getLine());
            return "";
        }
    }

    /**
     * @notes 图片地址处理
     * @param $image
     * @return string
     * @author mjf
     * @date 2023/9/26 15:56
     */
    public static function imageUrlTrim($image, $flag = false)
    {
        if ($flag) {
            $check = strpos($image, '?ex=');
            if ($check !== false) {
                return mb_substr($image, 0, $check);
            }
        }
        return $image;
    }

    /**
     * @notes 意间绘画风格选择
     * @return array|mixed
     * @author mjf
     * @date 2023/11/7 17:01
     */
    public static function getSelector()
    {
        $resData = [
            'detail' => [
                'common' => [],
                'cartoon' => [],
                'reality' => [],
            ],
        ];

        try {
            $cache = new DrawYjSelectorCache();
            $cacheData = $cache->getSelector();
            if (!empty($cacheData)) {
                $resData['detail'] = $cacheData;
                return $resData;
            }

            $file = file_get_contents('./resource/other/yijian.json');
            $data = json_decode($file, true);

            if (!empty($data)) {
                $cache->setSelector($data);
            }

            $resData['detail'] = $data;
            return $resData;

        } catch (\Exception $e) {
            return $resData;
        }
    }

    /**
     * @notes sd模型
     * @return array
     * @author mjf
     * @date 2024/1/4 15:39
     */
    public static function getSdModel()
    {
        try {
            $apiConfig = ConfigService::get('draw_config', DrawEnum::API_SD, []);
            if (empty($apiConfig['proxy_url'])) {
                return [];
            } else {
                $engine = new DrawSd($apiConfig['proxy_url']);
                $models = $engine->getModel();

                $data = [];
                if (!empty($models)) {
                    foreach ($models as $model) {
                        $data[] = [
                            'title' => $model['title'],
                            'model_name' => $model['model_name'],
                        ];
                    }
                }
                return $data;
            }
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * @notes 失败记录处理
     * @param $record
     * @param array $otherUpdateData
     * @author mjf
     * @date 2024/1/4 16:10
     */
    public static function failRecordHandle($record, array $otherUpdateData = [])
    {
        $updateData = [
            'status' => DrawEnum::STATUS_FAIL,
            'update_time' => time(),
        ];
        $updateData = array_merge($updateData, $otherUpdateData);
        DrawRecords::where('id', $record['id'])->update($updateData);

        self::drawBalanceHandle(
            $record['user_id'],
            $record['use_tokens'],
            AccountLogEnum::DRAW_INC_DRAW_FAIL
        );

        self::drawTotalHandle($record['user_id'], AccountLogEnum::DRAW_INC_DRAW_FAIL);
    }

    /**
     * @notes 提示词校验
     * @param string $prompt
     * @return bool
     * @author mjf
     * @date 2024/1/24 11:15
     */
    public static function promptCensor(string $prompt): bool
    {
        try {
            // 敏感词审核
            AiChatService::Sensitive($prompt);

            // 内容审核
            $promptOpen = ConfigService::get('content_censor', 'prompt_open', 0);
            if ($promptOpen) {
                AiChatService::contentCensor($prompt);
            }
            return true;
        } catch (\Exception $e) {
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * @notes 审核提示词或图片
     * @param int $recordId
     * @author mjf
     * @date 2024/1/26 10:40
     */
    public static function imageCensor(int $recordId, string $fileUrl = ''): void
    {
        $imageOpen = ConfigService::get('content_censor', 'image_open', 0);
        if (!$imageOpen) {
            return;
        }

        $drawRecord = (new DrawRecords)->findOrEmpty($recordId);
        if ($drawRecord->isEmpty() || empty($drawRecord['image'])) {
            return;
        }

        // 错误
        $failReason = '';
        // 图片
        $image = FileService::getFileUrl($drawRecord['image']);
        if (!empty($fileUrl)) {
            $image =  FileService::format($fileUrl, FileService::setFileUrl($drawRecord['image']));
        }

        // 合规
        $censorStatus = ContentCensorEnum::CENSOR_STATUS_COMPLIANCE;

        try {
            // 审核
            AiChatService::contentCensor($image, ContentCensorEnum::TYPE_IMAGE);
        } catch (\Exception $e) {
            // 其审核失败
            $censorStatus = ContentCensorEnum::CENSOR_STATUS_FAIL;
            if (!empty($e->getCode()) && $e->getCode() == 10007) {
                // 不合规
                $censorStatus = ContentCensorEnum::CENSOR_STATUS_NON_COMPLIANCE;
            }
            // 错误
            $failReason = $e->getMessage();
        }

        // 更新状态
        DrawRecords::where(['id' => $recordId])->update([
            'fail_reason' => $failReason,
            'censor_status' => $censorStatus,
        ]);
    }


    /**
     * @notes 用户是否为会员
     * @param $userId
     * @return bool
     * @author mjf
     * @date 2024/1/26 17:28
     */
    public static function userIsMember($userId): bool
    {
        $user = (new User)->findOrEmpty($userId);

        if ($user->isEmpty()) {
            return false;
        }

        if ($user->member_perpetual || ($user->member_end_time && $user->member_end_time > time())) {
            return true;
        }

        return false;
    }

}
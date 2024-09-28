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

use app\common\enum\qrcode\MewxEnum;
use app\common\enum\qrcode\QrcodeEnum;
use app\common\enum\qrcode\ZsyEnum;
use app\common\enum\user\AccountLogEnum;
use app\common\enum\YesNoEnum;
use app\common\logic\AccountLogLogic;
use app\common\logic\BaseLogic;
use app\common\model\qrcode\QrcodeRecords;
use app\common\model\user\User;
use app\common\service\FileService;
use app\common\service\qrcode\engine\QrcodeMewx;
use app\common\service\qrcode\QrcodeDriver;
use app\common\service\ConfigService;
use app\common\service\storage\Driver as StorageDriver;
use think\Exception;
use think\facade\Db;
use think\facade\Log;

/**
 * 艺术二维码逻辑
 * Class ArtCodeLogic
 * @package app\api\logic
 */
class QrcodeLogic extends BaseLogic
{
    /**
     * @notes 生成图片
     * @param $userId
     * @param $params
     * @return array|false
     * @author mjf
     * @date 2023/10/18 14:17
     */
    public static function imagine($userId, $params)
    {
        //校验能否绘画
        $checkResult = self::checkAbleDraw($userId, $params);
        if ($checkResult !== true) {
            self::$error = $checkResult;
            return false;
        }

        // 写入绘画记录，扣除用户余额 (内含事务)
        $recordData = self::drawRecordHandle($userId, $params);
        if ($recordData === false) {
            return false;
        }

        // 发起绘图请求
        $drawRes = self::drawImagineHandle($recordData);
        if (false === $drawRes) {
            return false;
        }

        return ['records_id' => (int)$recordData['id']];
    }

    /**
     * @notes 提交校验
     * @param $userId
     * @param $params
     * @return bool|string
     * @author mjf
     * @date 2023/10/18 9:54
     */
    public static function checkAbleDraw($userId, $params)
    {
        $user = User::where('id', $userId)->findOrEmpty()->toArray();
        if (empty($user)) {
            return '非法会员';
        }
        if(YesNoEnum::YES == $user['is_blacklist']) {
            return '您已被管理员禁止生成，请联系客服详询原因。';
        }

        // 校验功能是否已关闭
        $openConfig = ConfigService::get('art_qrcode_config', 'is_open', 0);
        if ($openConfig != 1) {
            return "艺术二维码功能已关闭";
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

        // 比较用户余额
        $needBalance = $drawBillConfig['balance'] ?? 1;
        if ($user['balance_draw'] < $needBalance) {
            return '余额不足';
        }
        return true;
    }

    /**
     * @notes 写入绘画记录
     * @param $userId
     * @param $params
     * @return QrcodeRecords|false|\think\Model
     * @author mjf
     * @date 2023/10/18 11:36
     */
    public static function drawRecordHandle($userId, $params)
    {
        Db::startTrans();
        try {
            // 如果开启模型计费则使用
            $drawModelConfig = self::modelConfig($userId, $params['model']);

            if (false == $drawModelConfig['member_free']) {
                // 扣除用户余额
                self::drawBalanceHandle(
                    $userId,
                    $drawModelConfig['balance'],
                    AccountLogEnum::DRAW_DEC_QRCODE_IMAGE
                );
            }

            // 累计绘画次数处理
            DrawLogic::drawTotalHandle($userId, AccountLogEnum::DRAW_DEC_QRCODE_IMAGE);

            // type 1-文本模式 2-图片模式
            $qrContent = '';
            $qrImage = '';
            if ($params['type'] == QrcodeEnum::TYPE_TEXT) {
                $qrContent = $params['qr_content'];
            } else {
                // 上传图片
                $qrImage = !empty($params['qr_image']) ? FileService::setFileUrl($params['qr_image']) : '';
            }

            $modelId = '';
            $templateId = '';
            $promptParams = $params['prompt_params'] ?? '';

            // 星月熊 模板及模型参数
            if ($params['model'] == QrcodeEnum::API_MEWX) {
                if ($params['way'] == QrcodeEnum::WAY_TEMPLATE) {
                    $promptParams = '';
                    $templateId = $params['template_id'] ?? '';
                } else {
                    $modelId = $params['model_id'] ?? '';
                }
            }

            // 知数云
            $aspectRatio = '';
            $pixelStyle = '';
            $markerShape = '';
            if ($params['model'] == QrcodeEnum::API_ZHISHUYUN) {
                $templateId = $params['template_id'] ?? '';
                $aspectRatio = $params['aspect_ratio'] ?? '';
                $pixelStyle = $params['pixel_style'] ?? '';
                $markerShape = $params['marker_shape'] ?? '';
                $promptParams = '';
            }

            // 增加绘图记录
            $record = QrcodeRecords::create([
                'user_id' => $userId,
                'type' => $params['type'],
                'way' => $params['way'],
                'prompt' => $params['prompt'] ?? '',
                'prompt_params' => $promptParams,
                'qr_content' => $qrContent,
                'qr_image' => $qrImage,
                'model_id' => $modelId,
                'template_id' => $templateId,
                'status' => QrcodeEnum::STATUS_NOT,
                'use_tokens' => $drawModelConfig['balance'],
                'model' => $drawModelConfig['model'],
                'aspect_ratio' => $aspectRatio,
                'pixel_style' => $pixelStyle,
                'marker_shape' => $markerShape,
            ]);

            Db::commit();

            return $record;

        } catch (\Exception $e) {
            Db::rollback();
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * @notes 提交请求
     * @param $record
     * @return bool
     * @author mjf
     * @date 2023/10/18 14:16
     */
    public static function drawImagineHandle($record)
    {
        $response = [];
        try {
            // 绘画服务
            $drawDriver = new QrcodeDriver($record['model']);

            // 更新绘图记录
            switch ($record['model']) {
                // 星月熊
                case QrcodeEnum::API_MEWX:
                    $qrImage = '';
                    if ($record['type'] == QrcodeEnum::TYPE_IMAGE) {
                        $record['qr_image'] = FileService::getFileUrl($record['qr_image']);
                        $qrImage = imgToBase64($record['qr_image'], false, false);
                        if ($qrImage === false) {
                            throw new Exception('上传图片异常');
                        }
                    }

                    $modelId = '';
                    $templateId = '';
                    $prompt = $record['prompt'];
                    if ($record['way'] == QrcodeEnum::WAY_SELF) {
                        $modelId = $record['model_id'];
                        $prompt .=  ' ' . $record['prompt_params'];
                    } else {
                        $templateId = $record['template_id'];
                    }

                    // 提交请求
                    $response = $drawDriver->imagine([
                        // 关键词
                        'prompt' => $prompt,
                        // 二维码内容
                        'qr_content' => $record['qr_content'],
                        // 二维码图片 (图片模式,二维码需处理为base64提交)
                        'qr_image' => $qrImage,
                        // 模型id
                        'model' => $modelId,
                        // 模板id
                        'template_id' => $templateId,
                    ]);

                    // 更新记录状态
                    QrcodeRecords::where(['id' => $record['id']])->update([
                        'task_id' => $response['data']['img_uuid'] ?? '',
                        'notify_snap' => json_encode($response, JSON_UNESCAPED_UNICODE),
                        'status' => QrcodeEnum::STATUS_IN_PROGRESS,
                        'update_time' => time(),
                    ]);
                    break;
                // 知数云
                case QrcodeEnum::API_ZHISHUYUN:
                    // 提交请求
                    $response = $drawDriver->imagine([
                        // 关键词
                        'prompt' => $record['prompt'],
                        // 二维码内容
                        'qr_content' => $record['qr_content'],
                        // 模板
                        'template_id' => $record['template_id'],
                        // 宽高比
                        'aspect_ratio' => $record['aspect_ratio'],
                        // 二维码像素风格
                        'pixel_style' => $record['pixel_style'],
                        // 二维码框风格
                        'marker_shape' => $record['marker_shape'],
                    ]);

                    // 更新记录状态
                    QrcodeRecords::where(['id' => $record['id']])->update([
                        'task_id' => $response['task_id'] ?? '',
                        'notify_snap' => json_encode($response, JSON_UNESCAPED_UNICODE),
                        'status' => QrcodeEnum::STATUS_IN_PROGRESS,
                        'update_time' => time(),
                    ]);
                    break;
            }

            $drawDriver->setKeyLog($record['id']);

            return true;

        } catch (\Exception $e) {
            self::$error = $e->getMessage();

            // 生成图片失败更新记录状态
            QrcodeRecords::where(['id' => $record['id']])->update([
                'status' => QrcodeEnum::STATUS_FAIL,
                'notify_snap' => !empty($response) ? json_encode($response, JSON_UNESCAPED_UNICODE) : [],
                'fail_reason' => $e->getMessage(),
                'update_time' => time(),
            ]);

            // 生成失败, 没有任务id,回退用户金额
            self::drawBalanceHandle(
                $record['user_id'], $record['use_tokens'],
                AccountLogEnum::DRAW_INC_DRAW_QRCODE_FAIL
            );

            // 生成失败， 扣减绘画次数
            DrawLogic::drawTotalHandle($record['user_id'],AccountLogEnum::DRAW_INC_DRAW_QRCODE_FAIL);

            return false;
        }
    }

    /**
     * @notes 扣除用户余额
     * @param $userId
     * @param $usedToken
     * @param $changeType
     * @author mjf
     * @date 2023/10/18 11:06
     */
    public static function drawBalanceHandle($userId, $usedToken, $changeType)
    {
        if ($usedToken <= 0) {
            return;
        }

        // 用户信息
        $user = User::findOrEmpty($userId);

        // $action 变动类型 $totalDraw 累计绘画消费 $balanceDraw 绘画余额
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
     * @notes 模型计费
     * @return array
     * @author mjf
     * @date 2023/10/18 9:52
     */
    public static function artQrcodeBillingConfig()
    {
        $isOpen = ConfigService::get('art_qrcode_config', 'billing_is_open', 0);
        $billingConfig = ConfigService::get('art_qrcode_config', 'billing_config', []);
        $defaultConfigLists = QrcodeEnum::getDefaultBillingConfig();

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
            'billing_config' => $billingModelList
        ];
    }

    /**
     * @notes 回调处理
     * @param $response
     * @return bool
     * @author mjf
     * @date 2023/10/18 15:04
     */
    public static function notifyMewx($response)
    {
        try {
            if (!isset($response['status'])) {
                throw new \Exception("回调参数status缺失");
            }

            if ($response['status'] != 1) {
                throw new \Exception('回调参数status非成功状态');
            }

            // 绘图记录
            $record = QrcodeRecords::where(['task_id' => $response['img_uuid']])->findOrEmpty();
            if ($record->isEmpty()) {
                throw new \Exception("绘图记录不存在");
            }

            // 已标记成功或失败的记录不处理
            if (in_array($record['status'], [QrcodeEnum::STATUS_FAIL, QrcodeEnum::STATUS_SUCCESS])) {
                throw new \Exception("绘图记录状态为已成功或失败，无需处理");
            }

            // 回调快照
            $oldNotifySnap = !empty($record['notify_snap']) ? $record['notify_snap'] : '';
            $nowNotifySnap = json_encode($response, JSON_UNESCAPED_UNICODE);
            $notifySnap = trim($oldNotifySnap . ',' . $nowNotifySnap, ',');

            // 更新信息
            $updateData = [
                'notify_snap' => $notifySnap,
                'update_time' => time(),
            ];
            // 下载到本地
            if (!empty($response['urls'][0])) {
                $updateData['image'] = self::downloadImage($response['urls'][0]);
                if (empty($updateData['image'])) {
                    $updateData['status'] = QrcodeEnum::STATUS_FAIL;
                    $updateData['fail_reason'] = "图片下载失败";
                } else {
                    $updateData['status'] = QrcodeEnum::STATUS_SUCCESS;
                }
            }

            QrcodeRecords::where(['id' => $record['id']])->update($updateData);

            return true;
        } catch (\Exception $e) {
            Log::write('星月熊回调失败:' . $e->getMessage() . $e->getLine());
            if (!empty($response)) {
                Log::write('星月熊回调失败:' . json_encode($response, JSON_UNESCAPED_UNICODE));
            }
            return false;
        }
    }

    /**
     * @notes 知数云回调
     * @param $response
     * @return bool
     * @author mjf
     * @date 2023/11/9 16:32
     */
    public static function notifyZsy($response)
    {
        try {
            if (!isset($response['success']) || empty($response['task_id'])) {
                throw new \Exception("回调参数缺失");
            }

            // 绘图记录
            $record = QrcodeRecords::where(['task_id' => $response['task_id']])->findOrEmpty();
            if ($record->isEmpty()) {
                throw new \Exception("绘图记录不存在");
            }

            // 已标记成功或失败的记录不处理
            if (in_array($record['status'], [QrcodeEnum::STATUS_FAIL, QrcodeEnum::STATUS_SUCCESS])) {
                throw new \Exception("绘图记录状态为已成功或失败，无需处理");
            }

            // 回调快照
            $oldNotifySnap = !empty($record['notify_snap']) ? $record['notify_snap'] : '';
            $nowNotifySnap = json_encode($response, JSON_UNESCAPED_UNICODE);
            $notifySnap = trim($oldNotifySnap . ',' . $nowNotifySnap, ',');

            // 更新信息
            $updateData = [
                'notify_snap' => $notifySnap,
                'update_time' => time(),
            ];
            // 下载到本地
            if (!empty($response['image_url'])) {
                $updateData['image'] = self::downloadImage($response['image_url']);
                if (empty($updateData['image'])) {
                    $updateData['status'] = QrcodeEnum::STATUS_FAIL;
                    $updateData['fail_reason'] = "图片下载失败";
                } else {
                    $updateData['status'] = QrcodeEnum::STATUS_SUCCESS;
                }
            }

            QrcodeRecords::where(['id' => $record['id']])->update($updateData);

            return true;

        } catch (\Exception $e) {
            Log::write('知数云艺术码回调失败:' . $e->getMessage() . $e->getLine());
            if (!empty($response)) {
                Log::write('知数云艺术码回调失败:' . json_encode($response, JSON_UNESCAPED_UNICODE));
            }
            return false;
        }
    }

    /**
     * @notes 下载图片
     * @param $downloadUrl
     * @return string
     * @author mjf
     * @date 2023/10/18 15:02
     */
    public static function downloadImage($downloadUrl, $isCli = false): string
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
                // 下载到本地,如果存储为oss时,生成缩略图后删除
                $saveDir = 'uploads/art_qrcode/';
                if($isCli) {
                    $downSaveDir = app()->getRootPath() . 'public/' . $saveDir . date('Ymd') . '/';
                    download_file($downloadUrl, $downSaveDir, $fileName, false);
                    return $saveDir . date('Ymd') . '/' . $fileName;
                } else {
                    return download_file($downloadUrl, $saveDir . date('Ymd') . '/', $fileName, false);
                }
            } else {
                $localPath = 'uploads/art_qrcode/' . date('Ymd') . '/' . $fileName;
                $StorageDriver = new StorageDriver($config);
                if (!$StorageDriver->fetch($downloadUrl, $localPath)) {
                    throw new \Exception('绘图保存失败:' . $StorageDriver->getError());
                }
                return $localPath;
            }

        } catch (\Exception $e) {
            Log::write('艺术二维码回调下载失败:' . $e->getMessage() . $e->getLine());
            return '';
        }
    }

    /**
     * @notes 模型配置
     * @return array
     * @author mjf
     * @date 2023/10/18 15:16
     */
    public static function modelConfig($userId, bool|string $drawModel = true)
    {
        $modelArr = [];
        $config = self::artQrcodeBillingConfig();

        $apiType = ConfigService::get('art_qrcode_config', 'type', QrcodeEnum::API_MEWX);
        $default = QrcodeEnum::getDefaultBillingConfig($apiType);

        $balance = 1;
        $memberFree = false;
        $memberStatus = DrawLogic::userIsMember($userId);
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
     * @notes 相关配置
     * @return array
     * @author mjf
     * @date 2023/10/18 15:39
     */
    public static function config($userId)
    {
        $mewxConfig = ConfigService::get('art_qrcode_config', QrcodeEnum::API_MEWX);
        $data = [
            'is_open' => ConfigService::get('art_qrcode_config', 'is_open', 0),
            'draw_model' => self::modelConfig($userId),
            'example' => [
                'status' => ConfigService::get('art_qrcode_config', 'example_status', 0),
                'content' => ConfigService::get('art_qrcode_config', 'example_content', ''),
            ],
            'mewx' => [
                'version' => MewxEnum::getVersion($mewxConfig['version'] ?? []),
                'model' => MewxEnum::getModel(),
                'template' => MewxEnum::getTemplate(),
            ],
            'zhishuyun_qrcode' => [
                'template' => ZsyEnum::getTemplate(),
                'pixel_style' => ZsyEnum::getPixelStyle()
            ],
        ];

        return $data;
    }

}
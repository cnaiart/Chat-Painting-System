<?php
// +----------------------------------------------------------------------
// | likeadmin快速开发前后端分离管理后台（PHP版）
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | gitee下载：https://gitee.com/likeshop_gitee/likeadmin
// | github下载：https://github.com/likeshop-github/likeadmin
// | 访问官网：https://www.likeadmin.cn
// | likeadmin团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeadminTeam
// +----------------------------------------------------------------------

namespace app\common\command;

use app\api\logic\DrawLogic;
use app\api\logic\QrcodeLogic;
use app\common\cache\KeyPoolCache;
use app\common\enum\KeyPoolEnum;
use app\common\enum\qrcode\QrcodeEnum;
use app\common\enum\user\AccountLogEnum;
use app\common\model\KeyLog;
use app\common\model\qrcode\QrcodeRecords;
use app\common\service\ConfigService;
use app\common\service\qrcode\engine\QrcodeMewx;
use app\common\service\qrcode\engine\QrcodeZsy;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Log;

/**
 * 艺术二维码失败处理
 * Class QrcodeFail
 * @package app\common\command
 */
class QrcodeFail extends Command
{
    protected function configure()
    {
        $this->setName('qrcode_fail')
            ->setDescription('处理未接收到回调的艺术二维码记录');
    }

    protected function execute(Input $input, Output $output)
    {
        $nowTime = time();
        $expireTime = 10 * 60;

        $records = QrcodeRecords::where(['status' => QrcodeEnum::STATUS_IN_PROGRESS])
            ->whereRaw("create_time + $expireTime < $nowTime")
            ->select()
            ->toArray();

        if (empty($records)) {
            return true;
        }

        foreach ($records as $record) {
            // 星月熊 查询详情确认是否生成失败
            if ($record['model'] == QrcodeEnum::API_MEWX && $this->mewxCheck($record) === true) {
                continue;
            }

            // 知数云 查询详情确认是否生成失败
            if ($record['model'] == QrcodeEnum::API_ZHISHUYUN && $this->zsyCheck($record) === true) {
                continue;
            }

            // 生成图片失败
            QrcodeRecords::where(['id' => $record['id']])->update([
                'status' => QrcodeEnum::STATUS_FAIL,
                'fail_reason' => '绘画服务响应失败',
                'update_time' => time(),
            ]);

            // 生成失败, 没有任务id,回退用户金额
            QrcodeLogic::drawBalanceHandle(
                $record['user_id'], $record['use_tokens'],
                AccountLogEnum::DRAW_INC_DRAW_QRCODE_FAIL
            );

            // 累计绘画次数处理
            DrawLogic::drawTotalHandle($record['user_id'], AccountLogEnum::DRAW_INC_DRAW_QRCODE_FAIL);
        }

        return true;
    }


    /**
     * @notes 星月熊查询详情
     * @param $record
     * @return bool
     * @author mjf
     * @date 2023/11/9 16:09
     */
    protected function mewxCheck($record)
    {
        try {
            $keyLog = KeyLog::where([
                'type' => KeyPoolEnum::TYPE_QRCODE,
                'record_id' => $record['id'],
                'ai_key' => QrcodeEnum::API_MEWX
            ])->findOrEmpty();

            if ($keyLog->isEmpty()) {
                return false;
            }

            $apiConfig = ConfigService::get('art_qrcode_config', QrcodeEnum::API_MEWX);
            if (!empty($apiConfig['proxy_url'])) {
                $qrcodeMewx = new QrcodeMewx($keyLog['key'], $apiConfig['proxy_url']);
            } else {
                $qrcodeMewx = new QrcodeMewx($keyLog['key']);
            }

            $detail = $qrcodeMewx->detail($record['task_id']);

            if (empty($detail['data']['urls'][0])) {
                return false;
            }

            // 生成图片成功
            $image = $detail['data']['urls'][0];
            // 回调快照
            $oldNotifySnap = !empty($record['notify_snap']) ? $record['notify_snap'] : '';
            $nowNotifySnap = json_encode($detail, JSON_UNESCAPED_UNICODE);
            $notifySnap = trim($oldNotifySnap . ',' . $nowNotifySnap, ',');

            // 更新信息
            $updateData = [
                'notify_snap' => $notifySnap,
                'update_time' => time(),
            ];

            // 下载到本地
            $updateData['image'] = QrcodeLogic::downloadImage($image, true);
            if (empty($updateData['image'])) {
                $updateData['status'] = QrcodeEnum::STATUS_FAIL;
                $updateData['fail_reason'] = "图片下载失败";
            } else {
                $updateData['status'] = QrcodeEnum::STATUS_SUCCESS;
            }
            QrcodeRecords::where(['id' => $record['id']])->update($updateData);
            return true;

        } catch (\Exception $e) {
            Log::write('查询艺术二维码状态:' . $e->getMessage());
            return false;
        }
    }


    /**
     * @notes 知数云查询
     * @param $record
     * @return bool
     * @author 段誉
     * @date 2024/2/7 13:22
     */
    protected function zsyCheck($record)
    {
        try {
            $keyLog = KeyLog::where([
                'type' => KeyPoolEnum::TYPE_QRCODE,
                'record_id' => $record['id'],
                'ai_key' => QrcodeEnum::API_ZHISHUYUN
            ])->findOrEmpty();

            if ($keyLog->isEmpty()) {
                return false;
            }

            $qrcodeZsy = new QrcodeZsy($keyLog['key']);

            $response = $qrcodeZsy->detail($record['task_id']);

            if (empty($response['response']) || empty($response['response']['image_url'])) {
                return false;
            }

            if (empty($response['response']['task_id'])) {
                $response['response']['task_id'] = $record['task_id'];
            }

            if (QrcodeLogic::notifyZsy($response['response'])) {
                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::write('查询艺术二维码状态:' . $e->getMessage());
            return false;
        }
    }


}
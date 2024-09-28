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
use app\common\enum\DrawEnum;
use app\common\enum\DrawTaskEnum;
use app\common\enum\KeyPoolEnum;
use app\common\enum\user\AccountLogEnum;
use app\common\model\draw\DrawRecords;
use app\common\model\draw\DrawTask;
use app\common\model\KeyLog;
use app\common\service\ConfigService;
use app\common\service\discord\DiscordSubmitService;
use app\common\service\draw\engine\DrawYj;
use app\common\service\draw\engine\DrawZsy;
use think\console\Command;
use think\console\Input;
use think\console\Output;

/**
 * 绘画失败处理
 * Class DrawFail
 * @package app\common\command
 */
class DrawFail extends Command
{
    protected function configure()
    {
        $this->setName('draw_fail')
            ->setDescription('处理未接收到回调的绘画记录');
    }

    protected function execute(Input $input, Output $output)
    {
        $nowTime = time();
        $timeOutConfig = ConfigService::get('draw_config', 'time_out', 10);
        $expireTime = $timeOutConfig * 60;

        $records = DrawRecords::where(['status' => DrawEnum::STATUS_IN_PROGRESS])
            ->whereRaw("create_time + $expireTime < $nowTime")
            ->select()
            ->toArray();

        if (empty($records)) {
            return true;
        }

        foreach ($records as $record) {
            // 意间绘画，查询详情
            if ($record['model'] == DrawEnum::API_YIJIAN_SD) {
                // 查询详情，判断是否有图片信息
                $flag = $this->yjCheck($record);
                if ($flag === true) {
                    continue;
                }
            }

            // mj直连,查询详情
            if ($record['model'] == DrawEnum::API_MDDAI_MJ) {
                $flag = $this->mjCheck($record);
                if ($flag === true) {
                    continue;
                } else {
                    $this->updateDrawTask($record['task_id'], DrawTaskEnum::STATUS_FAIL);
                }
            }

            // 知数云绘画，查询详情
            $zsyArr = [DrawEnum::API_ZHISHUYUN_TURBO, DrawEnum::API_ZHISHUYUN_FAST, DrawEnum::API_ZHISHUYUN_RELAX];
            if (in_array($record['model'], $zsyArr)) {
                // 查询详情，判断是否有图片信息
                $flag = $this->zsyCheck($record);
                if ($flag === true) {
                    continue;
                }
            }

            // 生成图片失败更新记录状态
            DrawRecords::where(['id' => $record['id']])->update([
                'status' => DrawEnum::STATUS_FAIL,
                'fail_reason' => '绘画服务响应失败',
                'update_time' => time(),
            ]);

            // 生成失败, 没有任务id,回退用户金额
            DrawLogic::drawBalanceHandle(
                $record['user_id'], $record['use_tokens'],
                AccountLogEnum::DRAW_INC_DRAW_FAIL
            );

            DrawLogic::drawTotalHandle($record['user_id'], AccountLogEnum::DRAW_INC_DRAW_FAIL);
        }

        return true;
    }


    /**
     * @notes 意间绘画检测
     * @param $record
     * @return bool
     * @author mjf
     * @date 2023/11/7 15:34
     */
    protected function yjCheck($record)
    {
        try {
            if (empty($record['task_id'])) {
                return false;
            }

            $keyLog = KeyLog::where([
                'type' => KeyPoolEnum::TYPE_DRAW,
                'record_id' => $record['id'],
                'ai_key' => DrawEnum::API_YIJIAN_SD
            ])->findOrEmpty();

            if ($keyLog->isEmpty()) {
                return false;
            }

            // 查询详情
            $engine = new DrawYj($keyLog['key'], $keyLog['secret']);
            $response = $engine->detail($record['task_id']);

            if (empty($response['ImagePath']) || empty($response['ThumbImagePath'])) {
                return false;
            }

            // 下载原图
            $image = DrawLogic::downloadImage($response['ImagePath']);
            // 下载缩略图
            $thumbnail = DrawLogic::getThumbnail($response['ImagePath']);
            if (empty($thumbnail)) {
                $thumbnail = DrawLogic::downloadImage($response['ThumbImagePath'], 'uploads/thumbnail/');
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
            return true;

        } catch (\Exception $e) {
            return false;
        }
    }


    /**
     * @notes mj直连校验
     * @param $record
     * @return bool
     * @author mjf
     * @date 2023/11/22 15:00
     */
    protected function mjCheck($record)
    {
        try {
            if (empty($record['task_id'])) {
                return false;
            }

            $task = DrawTask::where(['task_id' => $record['task_id']])
                ->findOrEmpty()->toArray();

            if (empty($task) || empty($task['token'])) {
                return false;
            }

            // 查询，图片是否已生成但监听没捕获到信息
            $detail = (new DiscordSubmitService($task['token']))->search($task['task_id']);
            // 图片地址
            $imageUrl = $detail['messages'][0][0]['attachments'][0]['url'] ?? '';
            $imageUrl = $this->imageUrlTrim($imageUrl);
            // 信息id
            $msgId = $detail['messages'][0][0]['id'] ?? '';
            $msgHash = $this->getImageHash($imageUrl);

            if (empty($imageUrl) || empty($msgId)) {
                return false;
            }

            $extension = substr($imageUrl, strrpos($imageUrl, ".") + 1);
            if (strcmp($extension, "webp") == 0) {
                return false;
            }

            $res = DrawLogic::notifyMdd([
                'code' => 1,
                'msg' => "成功",
                'data' => [
                    'task_id' => $task['task_id'],
                    'action' => $task['action'],
                    'image_url' => $imageUrl,
                ],
            ]);

            if ($res) {
                DrawTask::update([
                    'id' => $task['id'],
                    'image_url' => $imageUrl,
                    'msg_hash' => $msgHash,
                    'msg_id' => $msgId,
                    'finish_time' => time(),
                    'status' => DrawTaskEnum::STATUS_SUCCESS,
                    'progress' => 100,
                ]);
                return true;
            }

            return false;

        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @notes 知数云查询
     * @param $record
     * @return bool
     * @author mjf
     * @date 2024/2/2 17:49
     */
    protected function zsyCheck($record)
    {
        try {
            if (empty($record['task_id'])) {
                return false;
            }

            $keyLog = KeyLog::where([
                'type' => KeyPoolEnum::TYPE_DRAW,
                'record_id' => $record['id'],
                'ai_key' => $record['model']
            ])->findOrEmpty();

            if ($keyLog->isEmpty()) {
                return false;
            }

            // 查询详情
            $engine = new DrawZsy($record['model']);
            $response = $engine->detail($record['task_id']);

            if (empty($response['response']) || empty($response['response']['image_url'])) {
                return false;
            }

            if (DrawLogic::notifyZsy($response['response'])) {
                return true;
            }
            return false;

        } catch (\Exception $e) {
            return false;
        }
    }


    /**
     * @notes 获取图片hash
     * @param string $imageUrl
     * @return false|string
     * @author mjf
     * @date 2023/10/9 14:45
     */
    protected function getImageHash(string $imageUrl)
    {
        $hashStartIndex = strrpos($imageUrl, "_");
        $hash = substr($imageUrl, $hashStartIndex + 1);
        return strstr($hash, ".", true);
    }

    /**
     * @notes 去除图片多余字符
     * @param $image
     * @return string
     * @author mjf
     * @date 2023/10/9 14:45
     */
    protected function imageUrlTrim($image)
    {
//        $check = strpos($image, '?ex=');
//        if ($check !== false) {
//            return mb_substr($image, 0, $check);
//        }
        return $image;
    }

    /**
     * @notes 更新绘画任务
     * @param $taskId
     * @param $status
     * @author mjf
     * @date 2023/12/8 15:41
     */
    protected function updateDrawTask($taskId, $status)
    {
        DrawTask::where(['task_id' => $taskId])->update([
            'status' => $status,
            'update_time' => time(),
        ]);
    }

}
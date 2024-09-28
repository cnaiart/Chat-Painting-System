<?php

namespace app\job;

use app\api\logic\DrawLogic;
use app\common\enum\DrawEnum;
use app\common\enum\DrawTaskEnum;
use app\common\model\draw\DrawRecords;
use app\common\model\draw\DrawTask;
use app\common\service\discord\DiscordSubmitService;
use think\facade\Log;
use think\queue\Job;

class MjJob
{
    public function fire(Job $job, array $data)
    {
        $attemptsNums = $job->attempts();
        echo "\n\n";
        echo "执行的任务: RT:$attemptsNums@taskId:" . $data['task_id'] . "\n";

        // 更新任务状态
        $taskModel = new DrawTask();
        $task = $taskModel->findOrEmpty($data['id']);
        if ($task->isEmpty()) {
            echo "任务不存在: " . $data['task_id'] . " \n";
            $job->delete();
            return;
        }

        // 非待处理状态
        if ($task->status != DrawTaskEnum::STATUS_NOT_START) {
            echo "任务非待处理状态: " . $data['task_id'] . " \n";
            $job->delete();
            return;
        }

        // 重试多次且超过记录创建时间15分钟即标记为失败
//        if ($job->attempts() > 3 && time() > strtotime($task->create_time) + 900) {
//            // 更新绘画记录
//            $task->fail_reason = "任务已重试多次且超过15分钟，标记失败";
//            $task->status = DrawTaskEnum::STATUS_FAIL;
//            $task->save();
//
//            // 删除任务
//            echo "任务已重试多次且超过15分钟，标记失败: " . $data['task_id'] . " \n";
//            $job->delete();
//
//            // 通知回调地址
//            $this->notifyFail($task, "服务繁忙,请重试");
//            return;
//        }

        // 更新失败任务
        $this->updateFailTask($task->token);

        // 查询执行中的数量
        $taskRunning = $taskModel->where('token', $task->token)
            ->whereIn('status', [
                DrawTaskEnum::STATUS_SUBMIT,
                DrawTaskEnum::STATUS_IN_PROGRESS
            ])->count();

        if ($taskRunning > 10) {
            echo "等待或运行中数量:" . $taskRunning . ",延迟10秒" . " \n";
            $job->release(10);
            return;
        }

        $task->status = DrawTaskEnum::STATUS_SUBMIT;
        $task->submit_time = time();
        $task->save();

        try {
            // 提交请求到discord
            $discordSubmit = new DiscordSubmitService($task->token);

            switch ($task['action']) {
                case DrawTaskEnum::ACTION_GENERATE:
                    $discordSubmit->imagine($task->prompt_desc);
                    break;
                case DrawTaskEnum::ACTION_UPSAMPLE:
                    $discordSubmit->upscale($task->image_msg_id, $task->index, $task->image_msg_hash);
                    break;
                case DrawTaskEnum::ACTION_VARIATION:
                    $discordSubmit->variate($task->image_msg_id, $task->index, $task->image_msg_hash);
                    break;
            }

        } catch (\Exception $e) {
            $errMsg = "任务提交失败: " . $task['task_id'] . ':__@__:' . $e->getMessage();
            Log::error($errMsg);
            echo $errMsg . "\n";

            // 更新任务信息
            $task->fail_reason = $e->getMessage();
            $task->status = DrawTaskEnum::STATUS_FAIL;
            $task->save();

            // 提交失败通知
            $this->notifyFail($task, $e->getMessage());

        } finally {
            // 删除任务
            $job->delete();
        }
    }

    private function notifyFail(DrawTask $task, $failMsg)
    {
        DrawLogic::notifyMdd([
            'code' => 0,
            'msg' => $failMsg,
            'data' => [
                'task_id' => $task['task_id'] ?? '',
            ],
        ]);
    }


    /**
     * @notes 更新失败任务
     * @param $token
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author mjf
     * @date 2023/12/18 16:20
     */
    private function updateFailTask($token)
    {
        $taskLists = DrawTask::where('token', $token)
            ->whereIn('status', [
                DrawTaskEnum::STATUS_SUBMIT,
                DrawTaskEnum::STATUS_IN_PROGRESS
            ])
            ->limit(100)
            ->select()
            ->toArray();

        if (empty($taskLists)) {
            return;
        }

        foreach ($taskLists as $task) {
            $records = DrawRecords::where('task_id', $task['task_id'])->findOrEmpty();
            if ($records->isEmpty()) {
                DrawTask::update([
                    'id' => $task['id'],
                    'fail_reason' => '绘画记录不存在或已删除',
                    'status' => DrawTaskEnum::STATUS_FAIL,
                ]);
                continue;
            }

            if (!in_array($records['status'], [DrawEnum::STATUS_FAIL, DrawEnum::STATUS_SUCCESS])) {
                continue;
            }

            $updateReason = '记录已标记失败';
            $updateStatus = DrawTaskEnum::STATUS_FAIL;

            if ($records['status'] == DrawEnum::STATUS_SUCCESS) {
                $updateReason = '记录已标记成功';
                $updateStatus = DrawTaskEnum::STATUS_SUCCESS;
            }

            DrawTask::update([
                'id' => $task['id'],
                'fail_reason' => $updateReason,
                'status' => $updateStatus,
            ]);
        }
    }

}
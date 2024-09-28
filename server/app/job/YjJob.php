<?php

namespace app\job;

use app\api\logic\DrawLogic;
use app\common\enum\DrawEnum;
use app\common\model\draw\DrawRecords;
use app\common\service\draw\DrawDriver;
use app\common\service\FileService;
use think\facade\Log;
use think\queue\Job;

class YjJob
{
    public function fire(Job $job, array $data)
    {
        $attemptsNums = $job->attempts();
        echo "\n\n";
        echo "执行的任务: RT:$attemptsNums@taskId:" . $data['id'] . "\n";

        // 更新任务状态
        $taskModel = new DrawRecords();
        $task = $taskModel->findOrEmpty($data['id']);
        if ($task->isEmpty()) {
            echo "任务不存在: " . $data['id'] . " \n";
            $job->delete();
            return;
        }

        if ($task->model != DrawEnum::API_YIJIAN_SD) {
            echo "任务非意间绘画模型: " . $data['id'] . " \n";
            $job->delete();
            return;
        }

        // 非待处理状态
        if ($task->status != DrawEnum::STATUS_IN_PROGRESS) {
            echo "任务非待处理状态: " . $data['id'] . " \n";
            $job->delete();
            return;
        }

        // 重试多次且超过记录创建时间15分钟即标记为失败
//        if ($job->attempts() > 5 && time() > strtotime($task->create_time) + 900) {
//            // 更新绘画记录
//            $task->fail_reason = "任务已重试多次或等待超过15分钟，标记失败";
//            $task->status = DrawEnum::STATUS_FAIL;
//            $task->save();
//
//            // 删除任务
//            echo "任务已重试多次且或等待超过15分钟，标记失败: " . $data['id'] . " \n";
//            $job->delete();
//            return;
//        }

        // 查询执行中的数量
        $taskRunning = $taskModel->whereIn('status', [
            DrawEnum::STATUS_IN_PROGRESS
        ])->where('model', DrawEnum::API_YIJIAN_SD)->count();

        if ($taskRunning > 10) {
            echo "等待或运行中数量:" . $taskRunning . ",延迟10秒" . " \n";
            $job->release(10);
            return;
        }

        try {
            // 提交请求到意间
            $drawEngine = new DrawDriver(DrawEnum::API_YIJIAN_SD, $data);
            $requestData = $task->toArray();
            $response = $drawEngine->imagine($requestData);

            // 更新任务为执行中状态
            $task->task_id = $response['Uuid'];
            $task->submit_time = time();
            $task->save();

            // 设置key使用日志
            $drawEngine->setKeyLog($task['id']);

        } catch (\Throwable $e) {
            $errMsg = "任务提交失败: " . $task['id'] . ':__@__:' . $e->getMessage();
            Log::error($errMsg);
            echo $errMsg . "\n";

            // 失败处理
            DrawLogic::failRecordHandle($task, [
                'fail_reason' => $e->getMessage(),
                'task_id' => $response['Uuid'] ?? ''
            ]);

        } finally {
            // 删除任务
            $job->delete();
        }
    }

}
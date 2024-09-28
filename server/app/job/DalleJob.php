<?php

namespace app\job;

use app\api\logic\DrawLogic;
use app\common\enum\DrawEnum;
use app\common\model\draw\DrawRecords;
use app\common\service\draw\DrawDriver;
use think\facade\Log;
use think\queue\Job;

class DalleJob
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

        if ($task->model != DrawEnum::API_DALLE3) {
            echo "任务非Dalle3绘画模型: " . $data['id'] . " \n";
            $job->delete();
            return;
        }

        // 非待处理状态
        if ($task->status != DrawEnum::STATUS_IN_PROGRESS) {
            echo "任务非待处理状态: " . $data['id'] . " \n";
            $job->delete();
            return;
        }

        // 查询执行中的数量
        $taskRunning = $taskModel->whereIn('status', [
            DrawEnum::STATUS_IN_PROGRESS
        ])->where('model', DrawEnum::API_DALLE3)->count();

        if ($taskRunning > 10) {
            echo "等待或运行中数量:" . $taskRunning . ",延迟10秒" . " \n";
            $job->release(10);
            return;
        }

        try {
            // 提交请求到意间
            $drawEngine = new DrawDriver(DrawEnum::API_DALLE3, $data);
            $requestData = $task->toArray();
            $response = $drawEngine->imagine([
                'prompt' => $requestData['prompt_desc'],
                'size' => $requestData['scale'],
                'quality' => $requestData['quality'],
                'style' => $requestData['style'],
            ]);

            // 处理绘画结果
            $requestData['file_domain'] = $data['file_domain'] ?? '';
            DrawLogic::notifyDalle3($requestData, $response);

            // 设置key使用日志
            $drawEngine->setKeyLog($task['id']);

        } catch (\Throwable $e) {
            $errMsg = "任务提交失败: " . $task['id'] . ':__@__:' . $e->getMessage();
            Log::error($errMsg);
            echo $errMsg . "\n";
            // 失败处理
            DrawLogic::failRecordHandle($task, ['fail_reason' => $e->getMessage()]);
        } finally {
            // 删除任务
            $job->delete();
        }
    }


}
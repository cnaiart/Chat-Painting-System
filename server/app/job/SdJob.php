<?php

namespace app\job;

use app\api\logic\DrawLogic;
use app\common\enum\DrawEnum;
use app\common\model\draw\DrawRecords;
use app\common\service\draw\DrawDriver;
use app\common\service\FileService;
use think\facade\Log;
use think\queue\Job;

class SdJob
{
    public function fire(Job $job, array $data)
    {
        $attemptsNums = $job->attempts();
        echo "\n\n";
        echo "执行的任务: RT:$attemptsNums@taskId:" . $data['id'] . "\n";

        if ($job->attempts() > 1) {
            $job->delete();
            return;
        }

        // 更新任务状态
        $taskModel = new DrawRecords();
        $task = $taskModel->findOrEmpty($data['id']);
        if ($task->isEmpty()) {
            echo "任务不存在: " . $data['id'] . " \n";
            $job->delete();
            return;
        }

        if ($task->model != DrawEnum::API_SD) {
            echo "任务非SD绘画模型: " . $data['id'] . " \n";
            $job->delete();
            return;
        }

        // 非待处理状态
        if ($task->status != DrawEnum::STATUS_IN_PROGRESS) {
            echo "任务非待处理状态: " . $data['id'] . " \n";
            $job->delete();
            return;
        }

        try {
            // 提交请求到意间
            $drawEngine = new DrawDriver(DrawEnum::API_SD, $data);
            $requestData = $task->toArray();

            // 处理上传图片 获取上传图片base64
            $image = '';
            if ($requestData['type'] == DrawEnum::TYPE_IMAGE_TO_IMAGE && !empty($requestData['image_base'])) {
                $image = FileService::getFileUrl($requestData['image_base']);
                $image = imgToBase64($image, false, false);
            }

            // 比例参数处理
            $width = '512';
            $height = '512';
            if (!empty($requestData['scale'])) {
                list($width, $height) = explode('x', $requestData['scale']);
            }

            // 绘画模型
            $engine = '';
            if (!empty($requestData['engine'])) {
                $engine = $requestData['engine'];
            }

            $response = $drawEngine->imagine([
                'prompt' => $requestData['prompt_desc'],
                'width' => $width,
                'height' => $height,
                'engine' => $engine,
                'image' => $image
            ]);

            // 处理绘画结果
            $requestData['file_domain'] = $data['file_domain'] ?? '';
            DrawLogic::notifySd($requestData, $response);

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
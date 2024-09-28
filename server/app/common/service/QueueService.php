<?php

namespace app\common\service;

use app\job\DalleJob;
use app\job\MjJob;
use app\job\SdJob;
use app\job\YjJob;

/**
 * 队列服务类
 */
class QueueService
{
    private static string $YJ_JOB = 'AiYjJob'; // 意间绘画任务
    private static string $MJ_JOB = 'AiMjJob'; // Mj绘画任务
    private static string $DALLE_JOB = 'AiDalleJob'; // Mj绘画任务
    private static string $SD_JOB = 'AiSdJob'; // sd绘画任务

    /**
     * @notes 意间绘画队列
     * @param array $data
     * @author mjf
     * @date 2023/11/3 17:42
     */
    public static function pushYj(array $data, $delay = 0)
    {
        queue(YjJob::class, $data, $delay, self::$YJ_JOB);
    }

    /**
     * @notes Mj绘画队列
     * @param array $data
     * @author mjf
     * @date 2023/11/3 17:42
     */
    public static function pushMj(array $data, $delay = 0)
    {
        queue(MjJob::class, $data, $delay, self::$MJ_JOB);
    }

    /**
     * @notes dalle3
     * @param array $data
     * @param int $delay
     * @author mjf
     * @date 2023/12/1 11:55
     */
    public static function pushDalle(array $data, $delay = 0)
    {
        queue(DalleJob::class, $data, $delay, self::$DALLE_JOB);
    }

    /**
     * @notes sd
     * @param array $data
     * @param int $delay
     * @author mjf
     * @date 2024/1/4 10:43
     */
    public static function pushSd(array $data, $delay = 0)
    {
        queue(SdJob::class, $data, $delay, self::$SD_JOB);
    }

}
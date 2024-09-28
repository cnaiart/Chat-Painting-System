<?php
// +----------------------------------------------------------------------
// | 控制台配置
// +----------------------------------------------------------------------
use app\common\command\{Crontab,
    DiscordListen,
    DrawCheck,
    DrawThumbnail,
    Password,
    QrcodeFail,
    QueryRefund,
    ContentCensor,
    MigrationData,
    WechatMerchantTransfer,
    DrawFail};

return [
    // 指令定义
    'commands' => [
        // 定时任务
        'crontab' => Crontab::class,
        // 退款查询
        'query_refund' => QueryRefund::class,
        // 内容审核
        'content_censor' => ContentCensor::class,
        // 商家转账到零钱查询
        'wechat_merchant_transfer' => WechatMerchantTransfer::class,
        // 修改超级管理员密码
        'password' => Password::class,
        //迁移数据
        'migration_data' => MigrationData::class,
        // 绘图失败处理
        'draw_fail' => DrawFail::class,
        // 绘画缩略图
        'draw_thumbnail' => DrawThumbnail::class,
        // 艺术二维码失败处理
        'qrcode_fail' => QrcodeFail::class,
        // discord服务
        'discord' => DiscordListen::class,
    ],
];

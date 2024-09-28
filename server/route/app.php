<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\facade\Console;
use think\facade\Route;
//定时任务
Route::rule('crontab', function () {
    Console::call('crontab');
});
//更新配置
Route::rule('migration', function () {
    Console::call('migration_data');
});
//更新绘画缩略图
Route::rule('thumbnail', function () {
    Console::call('draw_thumbnail');
});
// 管理后台
Route::rule('admin/:any', function () {
    return view(app()->getRootPath() . 'public/admin/index.html');
})->pattern(['any' => '\w+']);

// 手机端
Route::rule('mobile/:any', function () {
    return view(app()->getRootPath() . 'public/mobile/index.html');
})->pattern(['any' => '\w+']);

// PC端
Route::rule('/:any', function () {
    return view(app()->getRootPath() . 'public/index.html');
})->pattern(['any' => '\w+']);



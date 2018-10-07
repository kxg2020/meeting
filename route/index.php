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

\think\facade\Route::get('index', 'index/index');
\think\facade\Route::get('', 'index/index');
use think\facade\Route;

Route::get("/","index/index");

Route::group("/api/",function(){
    // 会议类型列表
    Route::get("meetingType/type/:key","MeetingType/meetingType");
    // 具体会议列表
    Route::get("meetingType/list/:type","MeetingType/meetingList");
    // 创建新会议
    Route::post("meetingRecord/new","MeetingRecord/meetingNew");

})->middleware(\app\index\service\Auth::class);

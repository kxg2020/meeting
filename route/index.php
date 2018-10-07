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

use think\facade\Route;

Route::get("/","index/index");

// 会议类型列表
Route::group("/api/",function(){

    Route::get("meetingType/type","MeetingType/meetingType");
    Route::get("meetingType/list","MeetingType/meetingList");
    Route::post("meetingRecord/new","MeetingRecord/meetingNew");

})->middleware(\app\index\service\Auth::class);

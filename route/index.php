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

Route::group("/api/",function(){

    Route::get("meetingTypes","MeetingType/meetingTypes");

    Route::get("meeting_record_list/:typeId","MeetingRecord/meetingRecordList");

    Route::post("meetingRecord/new","MeetingRecord/meetingNew");

    Route::get("department","Department/list");

})->middleware(\app\index\service\Auth::class);

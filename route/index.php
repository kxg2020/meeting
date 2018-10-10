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

Route::get("/test","index/test");

Route::get("/upload","static/uploads");

Route::group("/api/",function(){

    Route::get("meetingTypes","MeetingType/meetingTypes");

    Route::get("meetingRecord/:typeId","MeetingRecord/meetingRecordList");

    Route::post("meetingRecord/create","MeetingRecord/meetingCreate");

    Route::get("department","Department/departmentList");

    Route::post("upload","File/upload");

    Route::get("political","MeetingPolitical/politicalList");

    Route::get("member/:id","Department/departmentMember");

})->middleware(\app\index\service\Auth::class);

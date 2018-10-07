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

// ���������б�
Route::group("/api/",function(){

    Route::get("meeting/type","meeting/meetingType");
    Route::get("meeting/list","meeting/meetingList");

})->middleware(\app\index\service\Auth::class);

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

Route::get("/upload","static/uploads");

Route::group("/api/",function(){

    #首页会议类型
    Route::get("meetingTypes","MeetingType/meetingTypes");

    #用户会议列表
    Route::get("meetingRecord/:typeId/:pgNum/:pgSize","MeetingRecord/meetingRecordList");

    #用户创建会议
    Route::post("meetingRecord/create","MeetingRecord/meetingCreate");

    #重置部门列表
    Route::get("department","Department/departmentList");

    #上传文件图片
    Route::post("upload","File/upload");

    #会议议题类型
    Route::get("political","MeetingPolitical/politicalList");

    #部门成员列表
    Route::get("member/:id","Department/departmentMember");

    #首页会议权限
    Route::get("permission/:typeId","Permission/meetingPermission");

    #用户邀请部门
    Route::get("user/invitation/:id","User/userInvitationDepartment");

    #单个会议详情
    Route::get("meetingRecord/info/:meetingId","MeetingRecord/singleMeetingInfo");

    #会议议题详情
    Route::get("meetingRecord/issue/:meetingId","MeetingRecordInfo/meetingRecordIssueInfo");

})->middleware(\app\index\service\Auth::class);

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

    #��ҳ��������
    Route::get("meetingTypes","MeetingType/meetingTypes");

    #�û������б�
    Route::get("meetingRecord/:typeId/:pgNum/:pgSize","MeetingRecord/meetingRecordList");

    #�û���������
    Route::post("meetingRecord/create","MeetingRecord/meetingCreate");

    #���ò����б�
    Route::get("department","Department/departmentList");

    #�ϴ��ļ�ͼƬ
    Route::post("upload","File/upload");

    #������������
    Route::get("political","MeetingPolitical/politicalList");

    #���ų�Ա�б�
    Route::get("member/:id","Department/departmentMember");

    #��ҳ����Ȩ��
    Route::get("permission/:typeId","Permission/meetingPermission");

    #�û����벿��
    Route::get("user/invitation/:id","User/userInvitationDepartment");

    #������������
    Route::get("meetingRecord/info/:meetingId","MeetingRecord/singleMeetingInfo");

    #������������
    Route::get("meetingRecord/issue/:meetingId","MeetingRecordInfo/meetingRecordIssueInfo");

})->middleware(\app\index\service\Auth::class);

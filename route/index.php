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

#�����¼����
Route::get("meetingRecord/word","MeetingRecord/meetingRecordWord");

Route::group("/api/",function(){

    #��ҳ��������
    Route::get("meetingTypes","MeetingType/meetingTypes");

    #�û������б�
    Route::get("meetingRecord/list/:typeId","MeetingRecord/meetingRecordList");

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
    Route::get("meetingRecord/detail/:issueId","MeetingRecordInfo/meetingRecordIssueInfo");

    #�����û�ͶƱ
    Route::post("userVotes/create","UserVotes/createUserVotes");

    #�û�ɾ������
    Route::get("meetingRecord/delete/:meetingId","MeetingRecord/meetingDelete");

    #�������ż�¼
    Route::post("notice/create","Notice/createNotice");

    #ɾ�����ż�¼
    Route::get("notice/delete/:noticeId","Notice/deleteNotice");

    #������ϸ��Ϣ
    Route::get("notice/detail/:noticeId","Notice/detailNotice");

    Route::get("notice/list","Notice/noticeList");

})->middleware(\app\index\service\Auth::class);

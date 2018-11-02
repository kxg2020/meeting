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

Route::any("/address/book","AddressBook/addressBookModifiedNotify");

#??????????
Route::get("meetingRecord/word","MeetingRecord/meetingRecordWord");

Route::group("/api/",function(){

    #???????????
    Route::get("meetingTypes","MeetingType/meetingTypes");

    #????????б?
    Route::get("meetingRecord/list/:typeId","MeetingRecord/meetingRecordList");

    #???????????
    Route::post("meetingRecord/create","MeetingRecord/meetingCreate");

    #???ò????б?
    Route::get("department","Department/departmentList");

    #????????
    Route::post("upload","File/upload");

    #????????????
    Route::get("political","MeetingPolitical/politicalList");

    #???????б?
    Route::get("member/:id","Department/departmentMember");

    #??????????
    Route::get("permission/:typeId","Permission/meetingPermission");

    #?????????
    Route::get("user/invitation/:id","User/userInvitationDepartment");

    #????????????
    Route::get("meetingRecord/info/:meetingId","MeetingRecord/singleMeetingInfo");

    #????????????
    Route::get("meetingRecord/detail/:issueId","MeetingRecordInfo/meetingRecordIssueInfo");

    #?????????
    Route::post("userVotes/create","UserVotes/createUserVotes");

    #??????????
    Route::get("meetingRecord/delete/:meetingId","MeetingRecord/meetingDelete");

    #??????????
    Route::post("notice/create","Notice/createNotice");

    #?????????
    Route::get("notice/delete/:noticeId","Notice/deleteNotice");

    #??????????
    Route::get("notice/detail/:noticeId","Notice/detailNotice");

    Route::get("notice/list","Notice/noticeList");

    #新闻创建权限
    Route::get("notice/permission","Notice/noticeCreatePermission");

    Route::get("clear/cache","User/clearCache");


})->middleware(\app\index\service\Auth::class);

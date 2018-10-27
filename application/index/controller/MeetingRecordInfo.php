<?php
namespace app\index\controller;
use think\Db;
use think\facade\Request;

class MeetingRecordInfo extends Base{

    /*
     * 会议议题详情
     */
    public function meetingRecordIssueInfo(){
        $meetingId = Request::param("issueId");
       $result = \app\index\model\MeetingRecordInfo::getInstance()->meetingIssueInfo($meetingId);
        if($result["status"]){
            return $this->printResponse(200,$result["data"]);
        }
        return $this->printResponse($result["code"]);
    }
}
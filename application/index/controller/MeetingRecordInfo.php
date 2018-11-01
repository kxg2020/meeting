<?php
namespace app\index\controller;
use app\index\service\Tool;
use think\Db;
use think\facade\Cache;
use think\facade\Request;

class MeetingRecordInfo extends Base{

    /*
     * 会议议题详情
     */
    public function meetingRecordIssueInfo(){
        $meetingId = Request::param("issueId");
        $result    = Tool::getInstance()->jsonEncode(Cache::get("meeting-issue-".$meetingId));
        if(!$result){
            $result = \app\index\model\MeetingRecordInfo::getInstance()->meetingIssueInfo($meetingId);
            if($result["status"]){
                Cache::set("meeting-issue-".$meetingId,Tool::getInstance()->jsonEncode($result));
                return $this->printResponse(200,$result["data"]);
            }
            return $this->printResponse($result["code"]);
        }
        return $this->printResponse(200,$result["data"]);
    }
}
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
        $result    = Tool::getInstance()->jsonDecode(Cache::get("meeting-issue-".$meetingId));
        $result["data"]["edit"] = $this->editable($meetingId);
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

    /*
     * 是否编辑
     */
    private function editable($issueId){
        // 当前议题
        $currentIssueStatus = Db::name("user_votes")
            ->where(["meeting_info_id"=>$issueId,"user_id"=>Request::instance()->userId])
            ->find();
        // 当前会议
        $currentMeeting = Db::name("meeting_record_info")
            ->field("meeting_record_id")
            ->where(["id"=>$issueId])
            ->find();
        // 会议记录
        $meetingRecord = Db::name("meeting_record")
            ->field("start_time,end_time")
            ->where(["id"=>$currentMeeting["meeting_record_id"]])
            ->find();
        $editable = true;
        // 会议是否开始
        if($meetingRecord["start_time"] > time()){
            $editable = false;
        }
        // 会议是否过期
        if($meetingRecord["end_time"] < time()){
            $editable = false;
        }
        // 是否已经记录过
        if($currentIssueStatus){
            $editable = false;
        }
        return $editable;
    }
}
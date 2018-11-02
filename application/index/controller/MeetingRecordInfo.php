<?php
namespace app\index\controller;
use app\index\service\Tool;
use think\Db;
use think\facade\Cache;
use think\facade\Request;

class MeetingRecordInfo extends Base{

    /*
     * ������������
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
     * �Ƿ�༭
     */
    private function editable($issueId){
        // ��ǰ����
        $currentIssueStatus = Db::name("user_votes")
            ->where(["meeting_info_id"=>$issueId,"user_id"=>Request::instance()->userId])
            ->find();
        // ��ǰ����
        $currentMeeting = Db::name("meeting_record_info")
            ->field("meeting_record_id")
            ->where(["id"=>$issueId])
            ->find();
        // �����¼
        $meetingRecord = Db::name("meeting_record")
            ->field("start_time,end_time")
            ->where(["id"=>$currentMeeting["meeting_record_id"]])
            ->find();
        $editable = true;
        // �����Ƿ�ʼ
        if($meetingRecord["start_time"] > time()){
            $editable = false;
        }
        // �����Ƿ����
        if($meetingRecord["end_time"] < time()){
            $editable = false;
        }
        // �Ƿ��Ѿ���¼��
        if($currentIssueStatus){
            $editable = false;
        }
        return $editable;
    }
}
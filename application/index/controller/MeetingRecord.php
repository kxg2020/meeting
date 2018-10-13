<?php
namespace app\index\controller;

use app\index\model\MeetingType;
use app\index\service\Tool;
use think\facade\Request;

class MeetingRecord extends Base {


    public function meetingCreate(){
        $params = Tool::getInstance()->jsonDecode(file_get_contents("php://input"));
        $result = \app\index\model\MeetingRecord::getInstance()
            ->createMeetingRecord($params);
        return $this->printResponse($result["code"]);
    }


    public function meetingRecordList(){
        $typeId = Request::param("typeId");
        $params = $this->formatPage(Request::param());
        $records = \app\index\model\MeetingRecord::getInstance()->getMeetingRecords($typeId,$params);
        if($records["status"]){
            return $this->printResponse(200, $records["data"]);
        }
        return $this->printResponse();
    }

    public function singleMeetingInfo(){
        $meetingId = Request::param("meetingId");
        var_dump($meetingId);die;
    }
}
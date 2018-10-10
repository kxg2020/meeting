<?php
namespace app\index\controller;

use app\index\model\MeetingType;
use think\facade\Request;

class MeetingRecord extends Base {


    public function meetingCreate(){
       $result = \app\index\model\MeetingRecord::getInstance()
            ->createMeetingRecord(Request::post());
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
}
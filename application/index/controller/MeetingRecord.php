<?php
namespace app\index\controller;

use think\facade\Request;

class MeetingRecord extends Base {


    public function meetingCreate(){}


    public function meetingRecordList(){
        $typeId = Request::param("typeId");
        $records = \app\index\model\MeetingRecord::getInstance()->getMeetingRecords($typeId);
        if($records["status"]){
            return $this->printResponse(200, $records["data"]);
        }
        return $this->printResponse();
    }
}
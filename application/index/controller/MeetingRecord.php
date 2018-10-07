<?php
namespace app\index\controller;

use think\facade\Request;

class MeetingRecord extends Base {


    public function meetingNew(){
        $params = Request::post();
        var_dump($params);
    }

    public function meetingRecordList(){
        $records = \app\index\model\MeetingRecord::getInstance()
            ->getMeetingRecords(Request::param('typeId'));
        if($records["status"]){
            return $this->printResponse(200, $records["data"]);
        }
        return $this->printResponse();
    }
}
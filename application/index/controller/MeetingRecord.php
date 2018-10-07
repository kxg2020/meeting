<?php
namespace app\index\service;

use app\index\controller\Base;
use think\facade\Request;

class MeetingRecord extends Base{


    public function meetingNew(){
        $params = Request::post();
        var_dump($params);
    }

    public function meetingRecords(){
        $records = \app\index\model\MeetingRecord::getInstance()
            ->getMeetingRecords(Request::param('meeting_type_id'));
        if($records["status"]){
            return $this->printResponse(200, $records["data"]);
        }
        return $this->printResponse();
    }
}
<?php
namespace app\index\controller;
use think\facade\Request;

class MeetingType extends  Base{

    /*
     * 所有会议类型
     */
    public function meetingTypes(){
        $meeting = \app\index\model\MeetingType::getInstance()->meetingTypes();
        if($meeting["status"]){
            return $this->printResponse(200,$meeting["data"]);
        }
        return $this->printResponse();
    }

}


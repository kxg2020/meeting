<?php
namespace app\index\controller;
use think\facade\Request;

class MeetingType extends  Base{

    /*
     * 会议类型
     */
    public function meetingType(){
        $params = Request::get();
        $meeting = \app\index\model\MeetingType::getInstance()->meetingType($params);
        if($meeting["status"]){
           return $this->printResponse(200,$meeting["data"]);
        }
        return $this->printResponse(200,[]);
    }


    /*
     * 根据type_id获取会议列表
     */
    public function meetingList(){
        $meetingId = Request::post("id");

    }
}
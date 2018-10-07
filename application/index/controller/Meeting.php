<?php
namespace app\index\controller;

use app\index\service\Aes;
use think\facade\Request;

class Meeting extends  Base{

    /*
     * 会议类型
     */
    public function meetingType(){
        $meeting = \app\index\model\Meeting::getInstance()->meetingType();
        if($meeting["status"]){
           return $this->printResponse(200,$meeting["data"]);
        }
        return $this->printResponse(200,[]);
    }

    /*
     * 创建会议
     */
    public function meetingCreate(){

    }

    /*
     * 会议列表
     */
    public function meetingList(){
        $meetingId = Request::post("id");

    }
}
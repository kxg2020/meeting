<?php
namespace app\index\controller;
use think\facade\Request;

class MeetingType extends  Base{

    /*
     * ��������
     */
    public function meetingType(){
        $meeting = \app\index\model\MeetingType::getInstance()->meetingType();
        if($meeting["status"]){
           return $this->printResponse(200,$meeting["data"]);
        }
        return $this->printResponse(200,[]);
    }


    /*
     * ����type_id��ȡ�����б�
     */
    public function meetingList(){
        $meetingId = Request::post("id");

    }
}
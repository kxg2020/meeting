<?php
namespace app\index\controller;

use app\index\service\Aes;
use think\facade\Request;

class Meeting extends  Base{

    /*
     * ��������
     */
    public function meetingType(){
        $meeting = \app\index\model\Meeting::getInstance()->meetingType();
        if($meeting["status"]){
           return $this->printResponse(200,$meeting["data"]);
        }
        return $this->printResponse(200,[]);
    }

    /*
     * ��������
     */
    public function meetingCreate(){

    }

    /*
     * �����б�
     */
    public function meetingList(){
        $meetingId = Request::post("id");

    }
}
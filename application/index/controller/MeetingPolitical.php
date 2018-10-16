<?php
namespace app\index\controller;

class MeetingPolitical extends Base{

    /*
     * 议题类型
     */
    public function politicalList(){
        $result = \app\index\model\MeetingPolitical::getInstance()->getPoliticalList();
        if($result["status"]){
           return $this->printResponse(200,$result["data"]);
        }
        return $this->printResponse();
    }
}
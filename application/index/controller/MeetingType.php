<?php
namespace app\index\controller;
use think\facade\Request;

class MeetingType extends  Base{

    /*
     * all
     */
    public function meetingTypes(){
        return \app\index\model\MeetingType::getInstance()->meetingTypes();
    }
}
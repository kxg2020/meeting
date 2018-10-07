<?php
namespace app\index\service;

use app\index\controller\Base;
use think\facade\Request;

class MeetingRecord extends Base{

    /*
   * 创建会议
   */
    public function meetingNew(){
        $params = Request::post();
        var_dump($params);
    }
}
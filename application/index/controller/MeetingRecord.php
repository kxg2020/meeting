<?php
namespace app\index\service;

use app\index\controller\Base;
use think\facade\Request;

class MeetingRecord extends Base{

    /*
   * ��������
   */
    public function meetingNew(){
        $params = Request::post();
        var_dump($params);
    }
}
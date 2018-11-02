<?php
namespace app\index\controller;

use think\facade\Cache;
use think\facade\Request;

class User extends Base{

    /*
     * ������������Ĳ���
     */
    public function userInvitationDepartment(){
        $meetingType = Request::param("id");
        $result = \app\index\model\User::getInstance()
            ->invitationDepartment($meetingType);
        if($result["data"]){
            return $this->printResponse(200,$result["data"]);
        }
        return $this->printResponse();
    }


    /*
     * ��ջ���
     */
    public function clearCache(){
        Cache::clear();
    }
}
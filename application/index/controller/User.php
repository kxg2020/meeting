<?php
namespace app\index\controller;

use think\facade\Request;

class User extends Base{

    public function userInvitationDepartment(){
        $meetingType = Request::param("id");
        $result = \app\index\model\User::getInstance()
            ->invitationDepartment($meetingType);
        if($result["data"]){
            return $this->printResponse(200,$result["data"]);
        }
        return $this->printResponse();
    }


}
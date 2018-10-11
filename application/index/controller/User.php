<?php
namespace app\index\controller;

class User extends Base{

    public function userInvitationDepartment(){
        $result = \app\index\model\User::getInstance()->invitationDepartment();
        if($result["data"]){
            return $this->printResponse(200,$result["data"]);
        }
        return $this->printResponse();
    }


}
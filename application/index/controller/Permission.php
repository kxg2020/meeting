<?php
namespace app\index\controller;

use think\facade\Request;

class Permission extends Base{

    public function meetingPermission(){
        $meetingTypeId = Request::param("typeId");
        $result = \app\index\model\Permission::getInstance()->getUserPermission($meetingTypeId);
        if($result["status"]){
            return $this->printResponse(200);
        }
        return $this->printResponse($result["code"]);
    }
}
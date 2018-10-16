<?php
namespace app\index\controller;

use think\Db;
use think\facade\Request;

class Permission extends Base{

    /*
     * 是否有创建会议权限
     */
    public function meetingPermission(){
        $meetingTypeId = Request::param("typeId");
        $result = \app\index\model\Permission::getInstance()->getUserPermission($meetingTypeId);
        if($result["status"]){
            return $this->printResponse(200,$result["data"]);
        }
        return $this->printResponse($result["code"],$result["data"]);
    }


}
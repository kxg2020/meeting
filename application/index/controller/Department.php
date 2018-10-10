<?php
namespace app\index\controller;

use app\index\service\WeChat;
use think\facade\Request;

class Department extends Base{

    public function departmentList(){
        $result = \app\index\model\Department::getInstance()->getDepartmentList();
        if($result["status"]){
           return $this->printResponse(200,$result["data"]["department"]);
        }
       return $this->printResponse();
    }

    public function departmentMember(){
        $departmentId = Request::param("id");
        $result = \app\index\model\Department::getInstance()->departmentMember($departmentId);
        if($result["status"]){
            return $this->printResponse(200,$result["data"]);
        }
        return $this->printResponse();
    }
}
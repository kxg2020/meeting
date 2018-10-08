<?php
namespace app\index\controller;

class Department extends Base{

    public function departmentList(){
        $result = \app\index\model\Department::getInstance()->getDepartmentList();
        if($result["status"]){
           return $this->printResponse(200,$result["data"]["department"]);
        }
       return $this->printResponse();
    }


}
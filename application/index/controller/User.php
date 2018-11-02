<?php
namespace app\index\controller;

use think\facade\Cache;
use think\facade\Request;

class User extends Base{

    /*
     * 创建会议邀请的部门
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
     * 清空缓存
     */
    public function clearCache(){
        Cache::clear();
    }
}
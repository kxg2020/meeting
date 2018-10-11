<?php
namespace app\index\model;

use app\index\service\Singleton;
use app\index\service\Tool;
use think\Db;

class Permission extends Base{

    use Singleton;

    public function getUserPermission($meetingTypeId){
        $userId = request()->userId;
        $user = Db::name("user")->where(["user_id"=>$userId])->find();
        if($user){
            $permission = Tool::getInstance()->jsonDecode($user["department"]);
            if(in_array($meetingTypeId,$permission)){
                return $this->returnResponse();
            }
            return $this->returnResponse([],4004);
        }
        return $this->returnResponse([],4003);
    }
}
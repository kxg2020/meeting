<?php
namespace app\index\model;
use app\index\service\Enum;
use app\index\service\Singleton;
use app\index\service\Tool;
use think\Db;

class Permission extends Base{
    use Singleton;
    private $superAdmin = 1;

    public function getUserPermission($meetingTypeId){
        $userId = request()->userId;
        $user = Db::name("user")->where(["user_id"=>$userId])->find();
        $permission = [];
        if($user){
            if($user["position"] == Enum::ADMIN){
                $permission["sponsored"] = true;
            }elseif(substr_count($user["enable_sponsored_meeting_type_id"],$meetingTypeId)){
                $permission["sponsored"] = true;
            }else{
                $permission["sponsored"] = false;
            }

            if($user["position"] == Enum::ADMIN){
                $permission["delete"] = true;
                $permission["export"] = true;
            }else{
                $permission["delete"] = false;
                $permission["export"] = false;
            }

            return $this->returnResponse($permission);
        }
        return $this->returnResponse(["sponsored" => false,"delete"=>false],4003);
    }
}
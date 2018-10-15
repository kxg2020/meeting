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
            if(substr_count($user["enable_sponsored_meeting_type_id"],$meetingTypeId)){
                return $this->returnResponse(["sponsored" => true]);
            }
            return $this->returnResponse(["sponsored" => false],4004);
        }
        return $this->returnResponse(["sponsored" => false],4003);
    }
}
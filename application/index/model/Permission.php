<?php
namespace app\index\model;
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
            if(substr_count($user["enable_sponsored_meeting_type_id"],$meetingTypeId)){
                $permission["sponsored"] = true;
            }else{
                $permission["sponsored"] = false;
            }
            if(!empty($user["department"])){
                $department = Tool::getInstance()->jsonDecode($user["department"]);
                if(in_array($this->superAdmin,$department)){
                    $permission["delete"] = true;
                }else{
                    $permission["delete"] = false;
                }
            }
            return $this->returnResponse($permission);
        }
        return $this->returnResponse(["sponsored" => false,"delete"=>false],4003);
    }
}
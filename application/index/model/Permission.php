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
            $departmentId = [];
            $department = MeetingType::getInstance()->getMeetingTypeDepartmentId($meetingTypeId);
            if(!empty($department["data"])){
                foreach ($department["data"] as $value){
                    $departmentId[] = $value["department_id"];
                }
            }
            $userDepartmentId = Tool::getInstance()->jsonDecode($user["department"]);
            if(array_intersect($departmentId,$userDepartmentId)){
                if(substr_count($user["enable_sponsored_meeting_type_id"],$meetingTypeId)){
                    return $this->returnResponse(["sponsored" => true]);
                }
                return $this->returnResponse();
            }
            return $this->returnResponse(["sponsored" => false],4004);
        }
        return $this->returnResponse(["sponsored" => false],4003);
    }
}
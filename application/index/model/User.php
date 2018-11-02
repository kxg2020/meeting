<?php
namespace app\index\model;
use app\index\service\Singleton;
use app\index\service\Tool;
use think\Db;
use think\facade\Cache;

class User extends Base{
    use Singleton;
    private $userInfo;

    /*
     * 查询用户
     */
    public function getUserByUserId($userId){
        $user = Tool::getInstance()->jsonDecode(Cache::get($userId));
        if(!$user){
            $user = Db::name("user")->where(["user_id"=>$userId])->find();
            Cache::set($userId,Tool::getInstance()->jsonEncode($user));
            return $this->returnResponse($user);
        }
        return $this->returnResponse($user);
    }


    /*
     * 用户详情
     */
    public function setUserInfo($params = []){
        $this->userInfo = [
            "user_id" => $params["userid"],
            "name"    => $params["name"],
            "avatar"  => str_replace("/0","/100",$params["avatar"]),
            "mobile"  => $params["mobile"],
            "enable"  => $params["enable"],
            "position"=> $params["position"],
            "department" => json_encode($params["department"]),
        ];
        return $this;
    }

    /*
     * 发起权限
     */
    public function updatePermission($userInfo){
        $meetingId = [];
        $meeting = PositionMeetingMap::getInstance()->getPositionMeeting($userInfo["Position"]);
        if(isset($meeting["data"]) && !empty($meeting["data"])){
            foreach ($meeting["data"] as $department){
                $meetingId[] = $department["id"];
            }
        }
        $meetingId = implode(",",$meetingId);
        $update = [
            "enable_sponsored_meeting_type_id" => $meetingId
        ];
        Db::name("user")->where(["user_id"=>$userInfo["UserID"]])->update($update);
        return $this;
    }

    /*
     * 邀请部门
     */
    public function invitationDepartment($meetingTypeId){
        $departmentName= [];
        // 用户邀请的部门
        $result = MeetingType::getInstance()
            ->getSingleMeetingTypeDepartmentName($meetingTypeId);
        if($result["data"]){
            // 部门成员(用于投票)
            $member = $this->departmentUserInDb($result["data"]["department_id"]);
            $departmentName["member"]  = $member;
            $departmentName["meeting"] = $result["data"];
            return $this->returnResponse($departmentName);
        }
        return $this->returnResponse();
    }

    /*
     * 所有用户
     */
    public function allUserInDatabase(){
        $result = Tool::getInstance()->jsonDecode(Cache::get("all-user"));
        if(!$result){
            $result = Db::name("user")->select();
            Cache::set("all-user",Tool::getInstance()->jsonEncode($result));
            return $this->returnResponse($result);
        }
        return $this->returnResponse($result);
    }

    /*
     * 部门成员
     */
    private function departmentUserInDb($departmentId){
        $userDepartment = Tool::getInstance()->jsonDecode(Cache::get("department-user"));
        if(!$userDepartment){
            $userAll = $this->allUserInDatabase("id,name,department");
            $userDepartment = [];
            if($userAll["data"]){
                foreach ($userAll["data"] as $key => $value){
                    $department = Tool::getInstance()->jsonDecode($value["department"]);
                    if(in_array($departmentId,$department)){
                        $userDepartment[] = $value;
                    }
                }
            }
            Cache::set("department-user",Tool::getInstance()->jsonEncode($userDepartment));
            return $userDepartment;
        }
        return $userDepartment;
    }
}
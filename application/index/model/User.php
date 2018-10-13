<?php
namespace app\index\model;
use app\index\service\Singleton;
use think\Db;

class User extends Base{
    use Singleton;
    private $userInfo;

    /*
     * 查询用户
     */
    public function getUserByUserId($userId){
        $user = Db::name("user")->where(["user_id"=>$userId])->find();
        return $this->returnResponse($user);
    }

    /*
     * 添加用户
     */
    public function createUser(){
        $result = Db::name("user")->insertGetId($this->userInfo);
        return $this->returnResponse($result);
    }

    /*
     * 更新用户
     */
    public function updateUser($userId){
        $result = Db::name("user")->where(["user_id"=>$userId])->update($this->userInfo);
        return $this->returnResponse($result);
    }


    /*
     * 用户详情
     */
    public function setUserInfo($params = []){
        $this->userInfo = [
            "user_id" => $params["userid"],
            "name"    => $params["name"],
            "avatar"  => $params["avatar"],
            "mobile"  => $params["mobile"],
            "enable"  => $params["enable"],
            "position"=> $params["position"],
            "department" => json_encode($params["department"]),
        ];
        return $this;
    }

    /*
     * 创建权限
     */
    public function updatePermission($userInfo){
        $meetingId = [];
        $meeting = PositionMeetingMap::getInstance()->getPositionMeeting($userInfo["position"]);
        if($meeting["data"]){
            foreach ($meeting["data"] as $department){
                $meetingId[] = $department["id"];
            }
        }
        $meetingId = implode(",",$meetingId);
        $update = [
            "enable_sponsored_meeting_type_id" => $meetingId
        ];
        Db::name("user")->where(["user_id"=>$userInfo["userid"]])->update($update);
        return $this;
    }

    /*
     * 邀请部门
     */
    public function invitationDepartment($meetingTypeId){
        $departmentName= [];
        $result = MeetingType::getInstance()
            ->getSingleMeetingTypeDepartmentName($meetingTypeId);
        if($result["data"]){
            $departmentName["meeting"] = $result["data"];
        }
        return $this->returnResponse($departmentName);
    }

    /*
     * 所有用户
     */
    public function allUserInDatabase($field){
        $result = Db::name("user")->field($field)->select();
        if($result){
            return $this->returnResponse($result);
        }
        return $this->returnResponse();
    }
}
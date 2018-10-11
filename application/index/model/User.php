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
     * 更新权限
     */
    public function updatePermission($userInfo){
        $meetingId = [];
        $meeting = PositionMeetingMap::getInstance()->getPositionMeeting($userInfo["position"]);
        if($meeting["data"]){
            foreach ($meeting["data"] as $department){
                $meetingId[] = $department["department_id"];
            }
        }
        $meetingId = implode(",",$meetingId);
        echo $meetingId;die;
        $update = [
            "enable_sponsored_meeting_id" => $meetingId
        ];
        Db::name("user")->where(["user_id"=>$userInfo["userid"]])->update($update);
        return $this;
    }
}
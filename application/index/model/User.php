<?php
namespace app\index\model;
use app\index\service\Singleton;
use think\Db;

class User extends Base{
    use Singleton;
    private $userInfo;

    /*
     * ��ѯ�û�
     */
    public function getUserByUserId($userId){
        $user = Db::name("user")->where(["user_id"=>$userId])->find();
        return $this->returnResponse($user);
    }

    /*
     * ����û�
     */
    public function createUser(){
        $result = Db::name("user")->insertGetId($this->userInfo);
        return $this->returnResponse($result);
    }

    /*
     * �����û�
     */
    public function updateUser($userId){
        $result = Db::name("user")->where(["user_id"=>$userId])->update($this->userInfo);
        return $this->returnResponse($result);
    }


    /*
     * �û�����
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
     * ����Ȩ��
     */
    public function updatePermission($position){
        $departmentId = [];
        $meeting = PositionMeetingMap::getInstance()->getPositionMeeting($position);
        if($meeting["data"]){
            foreach ($meeting["data"] as $department){
                $departmentId[] = $department["department_id"];
            }
        }
        $departmentId = implode(",",$departmentId);
        echo  $departmentId;
    }
}
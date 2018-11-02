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
     * ��ѯ�û�
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
     * �û�����
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
     * ����Ȩ��
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
     * ���벿��
     */
    public function invitationDepartment($meetingTypeId){
        $departmentName= [];
        // �û�����Ĳ���
        $result = MeetingType::getInstance()
            ->getSingleMeetingTypeDepartmentName($meetingTypeId);
        if($result["data"]){
            // ���ų�Ա(����ͶƱ)
            $member = $this->departmentUserInDb($result["data"]["department_id"]);
            $departmentName["member"]  = $member;
            $departmentName["meeting"] = $result["data"];
            return $this->returnResponse($departmentName);
        }
        return $this->returnResponse();
    }

    /*
     * �����û�
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
     * ���ų�Ա
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
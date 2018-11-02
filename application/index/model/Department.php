<?php
namespace app\index\model;
use app\index\service\Singleton;
use app\index\service\Tool;
use app\index\service\WeChat;
use think\Db;
use think\facade\Cache;

class Department extends Base{
    use Singleton;

    public function getDepartmentList(){
        $result = Tool::getInstance()->jsonDecode(Cache::get("department"));
        if($result){
            return $this->returnResponse($result);
        }else{
            $result = WeChat::getInstance()->getDepartmentList();
            if($result){
                Cache::set("department",Tool::getInstance()->jsonEncode($result));
                $this->createDepartment($result["department"]);
            }
            return $this->returnResponse($result);
        }
    }

    public function createDepartment($params){
        foreach ($this->yieldArray($params,count($params)) as $key => $value){
           if($value["parentid"] > 0){
               $record = Db::name("department")->field("id")->where(["department_id"=>$value["id"]])->find();
               $departmentData  = ["name" => $value["name"],"parent_id" => $value["parentid"],"department_id"=>$value["id"]];
               $meetingTypeData = ["title"=> $value["name"], "department_id" => $value["id"]];
               if($record){
                   // ¸üÐÂ
                   $meetingTypeData["update_time"] = time();
                   $departmentData["update_time"]  = time();
                   Db::name("department")->where(["department_id"=>$value["id"]])->update($departmentData);
                   Db::name("meeting_type")->where(["department_id"=>$value["id"]])->update($meetingTypeData);
               }else{
                   // ²åÈë
                   $meetingTypeData["create_time"] = time();
                   $departmentData["create_time"]  = time();
                   Db::name("department")->insert($departmentData);
                   Db::name("meeting_type")->insert($meetingTypeData);
               }
           }
        }
    }

    public function departmentMember($departmentId){
        $member = WeChat::getInstance()->getDepartmentMember($departmentId);
        if(isset($member["errcode"]) && $member["errcode"] == 0){
            return $this->returnResponse($member["userlist"]);
        }
        return $this->returnResponse();
    }

    private function yieldArray($params,$length){
        for($i = 0; $i < $length;$i++){
            yield $params[$i];
        }
    }

    public function departmentRealId($departmentId){
        $result = Db::name("department")->field("department_id")->where(["id" => $departmentId])->find();
        if($result){
            return $this->returnResponse($result["department_id"]);
        }
        return $this->returnResponse();
    }

    public function loginUserViewPermission($departmentId){
        $result = Db::name("department")
            ->alias("a")
            ->field("b.id as meetingTypeId")
            ->leftJoin("meeting_type b","a.department_id = b.department_id")
            ->where("a.department_id","in",$departmentId)
            ->select();
        if($result){
            return $this->returnResponse($result);
        }
        return $this->returnResponse();
    }
}
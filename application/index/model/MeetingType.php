<?php
namespace app\index\model;
use app\index\service\Singleton;
use app\index\service\Tool;
use think\Db;
use think\facade\Cache;

class MeetingType extends Base{
    use Singleton;

    public function meetingTypes(){
        $result = Tool::getInstance()->jsonDecode(Cache::get("meetingTypes"));
        if(!$result){
            $field  = "id,title,img_url";
            $result = Db::name("meeting_type")->field($field)->select();
            Cache::set("meetingTypes",Tool::getInstance()->jsonEncode($result),86400 * 7);
        }
        return $this->returnResponse($result);
    }

    public function getMeetingTypeById($id){
        $result = Db::name("meeting_type")->where(["id"=>$id])->find();
        return $this->returnResponse($result);
    }

    public function getMeetingTypeDepartmentId($meetingTypeId){
        $department = Db::name("meeting_type")->field("department_id")->where(["id" => $meetingTypeId])->select();
        if($department){
            return $this->returnResponse($department);
        }
        return $this->returnResponse();
    }

    public function getSingleMeetingTypeDepartmentName($meetingTypeId){
        $department = Db::name("meeting_type")
            ->alias("a")
            ->field("b.name,b.id,b.img_url")
            ->leftJoin("department b","a.department_id = b.department_id")
            ->where("a.id","in",$meetingTypeId)
            ->find();
        if($department){
            return $this->returnResponse($department);
        }
        return $this->returnResponse();
    }
}
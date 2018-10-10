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

}
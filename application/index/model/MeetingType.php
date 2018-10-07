<?php
namespace app\index\model;
use app\index\service\Singleton;
use think\Db;

class MeetingType extends Base{
    use Singleton;
    /*
     * 会议类型
     */
    public function meetingType(){
        $field  = "id,title,img_url";
        $result = Db::name("meeting_type")
            ->field($field)->where(["status" => 1])->select();
        return $this->returnResponse($result);
    }

    /*
     * 根据type_id获取列表
     */
    public function meetingList($meetingTypeId){
        $result = Db::name("");
    }
}
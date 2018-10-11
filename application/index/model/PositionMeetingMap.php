<?php
namespace app\index\controller;

use app\index\service\Singleton;
use think\Db;

class PositionMeetingMap extends \app\index\model\Base{
    use Singleton;
    public function getPositionMeeting($position){
        $meetingType = Db::name("position_meeting_map")
            ->alias("a")
            ->field("c.department_id ")
            ->leftJoin("meeting_type b","a.meeting_type_id = b.id")
            ->leftJoin("department c","b.department_id = c.department_id")
            ->where(["a.position"=>$position])
            ->select();
        if($meetingType){
            return $this->returnResponse($meetingType);
        }
        return $this->returnResponse();
    }
}
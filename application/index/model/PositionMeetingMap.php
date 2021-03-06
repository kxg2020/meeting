<?php
namespace app\index\model;
use app\index\service\Singleton;
use think\Db;

class PositionMeetingMap extends \app\index\model\Base{
    use Singleton;
    public function getPositionMeeting($position){
        $meetingType = Db::name("position_meeting_map")
            ->alias("a")
            ->field("b.id")
            ->leftJoin("meeting_type b","a.meeting_type_id = b.id")
            ->where(["a.position_name"=>$position])
            ->select();
        if($meetingType){
            return $this->returnResponse($meetingType);
        }
        return $this->returnResponse();
    }
}
<?php
namespace app\index\model;

use app\index\service\Singleton;
use think\Db;

class UserMeeting extends Base{
    use Singleton;

    public function createUserMeetingMap($params,$meetingRecordId){
        $insert = [
            "meeting_record_id" => $meetingRecordId,
            "user_id"     => $params["userid"],
            "create_time" => time(),
        ];
        $result = Db::name("user_meeting")->insertGetId($insert);
        if($result){
            return $this->returnResponse($result);
        }
        return $this->returnResponse();
    }
}
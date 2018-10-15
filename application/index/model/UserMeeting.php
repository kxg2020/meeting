<?php
namespace app\index\model;

use app\index\service\Singleton;
use think\Db;

class UserMeeting extends Base{
    use Singleton;

    public function createUserMeetingMap($userId,$meetingRecordId){
        $insert = [
            "meeting_record_id" => $meetingRecordId,
            "user_id"     => $userId,
            "create_time" => time(),
        ];
        $result = Db::name("user_meeting")->insertGetId($insert);
        if($result){
            return $this->returnResponse($result);
        }
        return $this->returnResponse();
    }

    public function userMeetingRecord($meetingRecordId){
        $result = Db::name("user_meeting")
            ->field("id")
            ->where(["meeting_record_id"=>$meetingRecordId])
            ->count();
        if($result){
            return $this->returnResponse(true);
        }
        return $this->returnResponse(false);
    }

    public function meetingJoinUser($meetingRecordId){
        $result = Db::name("user_meeting")
            ->alias("a")
            ->field("b.name,b.avatar,b.user_id")
            ->leftJoin("user b","a.user_id = b.user_id")
            ->where(["a.meeting_record_id" => $meetingRecordId])
            ->group("a.user_id")
            ->select();
        if($result){
            return $this->returnResponse($result);
        }
        return $this->returnResponse();
    }
}
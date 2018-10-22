<?php
namespace app\index\model;

use app\index\service\Singleton;
use think\Db;
use think\facade\Request;

class UserMeeting extends Base{
    use Singleton;

    public function createUserMeetingMap($userId,$meetingRecordId,$insertData = []){
        if($insertData){
            $insert = $insertData;
        }else{
            $insert = [
                "meeting_record_id" => $meetingRecordId,
                "user_id"     => $userId,
                "create_time" => time(),
            ];
        }
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

    public function updateUserMeetingStatus($params){
        $condition = [
            "user_id" => Request::instance()->userId,
            "meeting_record_id" => $params["meeting_record_id"]
        ];

        $result = Db::name("user_meeting")
            ->where($condition)
            ->update(["status" => 1]);
        if($result === false){
            return false;
        }
        return true;
    }
}
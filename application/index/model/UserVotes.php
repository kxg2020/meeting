<?php
namespace app\index\model;

use app\index\service\Enum;
use app\index\service\Singleton;
use think\Db;
use think\facade\Request;

class UserVotes extends Base{
    use Singleton;
    private $meeting;

    /*
     * ÓÃ»§Í¶Æ±
     */
    public function createUserVotes($params){
        $this->findAndCreate($params);
        switch ($params["issue_short_name"]){
            case Enum::READ:
                $this->createRead();
                break;
            case Enum::BALLOT:
                $this->createBallot($params);
                break;
            case Enum::VOTE:
                $this->createVote($params);
                break;
        }
        $result = $this->updateUserMeetingStatus($this->meeting);
        if($result){
            return $this->returnResponse([],9003);
        }
        return $this->returnResponse([],4007);
    }

    private function createRead(){

    }

    private function createBallot($params){
        if(isset($params["votes"]) && !empty($params["votes"])){
            $insert = [];
            array_walk($params["votes"],function ($value) use (&$insert,$params){
                $insert[] = [
                    'meeting_record_id' => $this->meeting["meeting_record_id"],
                    'meeting_info_id'   => $params["issue_id"],
                    "user_id"           => Request::instance()->userId,
                    "create_time"       => time(),
                    "type"              => Enum::BALLOT,
                    "choose"            => $value["select_index"],
                ];
            });
            Db::name("user_votes")->insertAll($insert);
        }
    }

    private function createVote($params){
        if(isset($params["votes"]) && !empty($params["votes"])){
            $insert = [];
            array_walk($params["votes"],function ($value) use (&$insert,$params){
                $insert[] = [
                    'meeting_record_id' => $this->meeting["meeting_record_id"],
                    'meeting_info_id'   => $params["issue_id"],
                    "user_id"           => Request::instance()->userId,
                    "create_time"       => time(),
                    "type"              => Enum::VOTE,
                    "choose"            => $value,
                ];
            });
            Db::name("user_votes")->insertAll($insert);
        }
    }

    private function findAndCreate($params){
        $meeting = Db::name("meeting_votes")
            ->field("a.id as votes_id,b.id as meeting_info_id,c.id as meeting_record_id")
            ->alias("a")
            ->leftJoin("meeting_record_info b","a.meeting_info_id = b.id")
            ->leftJoin("meeting_record c","b.meeting_record_id = c.id")
            ->where(["a.meeting_info_id" => $params["issue_id"]])
            ->find();
        $this->meeting = $meeting;
    }

    private function updateUserMeetingStatus($params){
      return  UserMeeting::getInstance()->updateUserMeetingStatus($params);
    }
}
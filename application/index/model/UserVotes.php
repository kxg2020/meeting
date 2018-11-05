<?php
namespace app\index\model;

use app\index\service\Enum;
use app\index\service\Singleton;
use app\index\service\Tool;
use think\Db;
use think\facade\Cache;
use think\facade\Request;

class UserVotes extends Base{
    use Singleton;
    private $meeting;
    private $params;

    /*
     * �û�ͶƱ
     */
    public function createUserVotes($params){
        $this->params = $params;
        // ��������
        $this->meetingDetail($params);
        switch ($params["issue_short_name"]){
            case Enum::READ:
                $this->createRead($params);
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

    private function createRead($params){
        $exist = $this->whetherRecordExist();
        if(!$exist){
            $insert = [
                'meeting_record_id' => $this->meeting["meeting_record_id"],
                'meeting_info_id'   => $params["issue_id"],
                "user_id"           => Request::instance()->userId,
                "create_time"       => time(),
                "type"              => Enum::READ,
            ];
            Db::name("user_votes")->insert($insert);
        }
    }

    private function createBallot($params){
        if(isset($params["votes"]) && !empty($params["votes"])){
            $exist = $this->whetherRecordExist();

            if(!$exist){
                $insert = [
                    'meeting_record_id' => $this->meeting["meeting_record_id"],
                    'meeting_info_id'   => $params["issue_id"],
                    "user_id"           => Request::instance()->userId,
                    "create_time"       => time(),
                    "type"              => Enum::BALLOT,
                    "choose"            => Tool::getInstance()->jsonEncode($params["votes"]),
                ];
                Db::name("user_votes")->insert($insert);
            }
        }
    }

    private function createVote($params){
        if(isset($params["votes"]) && !empty($params["votes"])){
            $exist = $this->whetherRecordExist();
            if(!$exist){
                $insert = [
                    'meeting_record_id' => $this->meeting["meeting_record_id"],
                    'meeting_info_id'   => $params["issue_id"],
                    "user_id"           => Request::instance()->userId,
                    "create_time"       => time(),
                    "type"              => Enum::VOTE,
                    "choose"            => Tool::getInstance()->jsonEncode($params["votes"]),
                ];
                Db::name("user_votes")->insert($insert);
            }
        }
    }

    private function meetingDetail($params){
        $filed = "b.id as meeting_info_id,b.meeting_record_id,b.title";
        $meeting = Db::name("meeting_record_info")
            ->field($filed)
            ->alias("b")
            ->where(["b.id" => $params["issue_id"]])
            ->find();
        $this->meeting = $meeting;
    }

    private function updateUserMeetingStatus($params){
      return  UserMeeting::getInstance()->updateUserMeetingStatus($params);
    }

    private function whetherRecordExist(){
        $condition = [
            "meeting_record_id" => $this->meeting["meeting_record_id"],
            "type"              => $this->params["issue_short_name"],
            "user_id"           => Request::instance()->userId,
            "meeting_info_id"   => $this->params["issue_id"],
        ];
        $record = Db::name("user_votes")->where($condition)->count();
        if($record){
            return true;
        }
        return false;
    }
}
<?php
namespace app\index\model;

use app\index\service\Enum;
use app\index\service\Singleton;
use think\Db;
use think\facade\Request;

class UserVotes extends Base{
    use Singleton;
    private $meeting;
    private $params;

    /*
     * 用户投票
     */
    public function createUserVotes($params){
        $this->params = $params;
        // 会议详情
        $this->findAndCreate($params);

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
        $exist = $this->whetherRecordExist("");
        if(!$exist){
            $insert = [
                'meeting_record_id' => $this->meeting["meeting_record_id"],
                'meeting_info_id'   => $params["issue_id"],
                "user_id"           => Request::instance()->userId,
                "create_time"       => time(),
                "type"              => Enum::READ,
                "vote_name"         => "",
                "issue_name"        => $this->meeting["title"],
            ];
            Db::name("user_votes")->insert($insert);
        }
    }

    private function createBallot($params){
        if(isset($params["votes"]) && !empty($params["votes"])){
            $insert = [];
            array_walk($params["votes"],function ($value) use (&$insert,$params){
                $exist = $this->whetherRecordExist($this->meeting["vote_name"]);
                if($exist){
                    $condition = [
                        "meeting_record_id" => $this->meeting["meeting_record_id"],
                        "type"              => $this->params["issue_short_name"],
                        "user_id"           => Request::instance()->userId,
                        "meeting_info_id"   => $this->params["issue_id"],
                        "issue_name"        => $this->meeting["title"],
                        "vote_name"         => $this->meeting["vote_name"],
                        "vote_id"           => $value["choose_id"]
                    ];
                    Db::name("user_votes")->where($condition)->update(["choose" => $value["select_index"]]);
                }else{
                    $insert = [
                        'meeting_record_id' => $this->meeting["meeting_record_id"],
                        'meeting_info_id'   => $params["issue_id"],
                        "user_id"           => Request::instance()->userId,
                        "create_time"       => time(),
                        "type"              => Enum::BALLOT,
                        "choose"            => $value["select_index"],
                        "vote_name"         => $this->meeting["vote_name"],
                        "issue_name"        => $this->meeting["title"],
                        "vote_id"           => $value["choose_id"]
                    ];
                    Db::name("user_votes")->insert($insert);
                }
            });

        }
    }

    private function createVote($params){
        if(isset($params["votes"]) && !empty($params["votes"])){
            $insert = [];
            array_walk($params["votes"],function ($value) use (&$insert,$params){
                $exist = $this->whetherRecordExist($this->meeting["vote_name"]);
                if($exist){
                    $condition = [
                        "meeting_record_id" => $this->meeting["meeting_record_id"],
                        "type"              => $this->params["issue_short_name"],
                        "user_id"           => Request::instance()->userId,
                        "meeting_info_id"   => $this->params["issue_id"],
                        "issue_name"        => $this->meeting["title"],
                        "vote_name"         => $this->meeting["vote_name"],
                        "vote_id"           => $value["choose_id"]
                    ];
                    Db::name("user_votes")->where($condition)->update(["choose" => $value]);
                }else{
                    $insert = [
                        'meeting_record_id' => $this->meeting["meeting_record_id"],
                        'meeting_info_id'   => $params["issue_id"],
                        "user_id"           => Request::instance()->userId,
                        "create_time"       => time(),
                        "type"              => Enum::VOTE,
                        "choose"            => $value,
                        "vote_name"         => $this->meeting["vote_name"],
                        "issue_name"        => $this->meeting["title"],
                        "vote_id"           => $value["choose_id"]
                    ];
                    Db::name("user_votes")->insert($insert);
                }
            });

        }
    }

    private function findAndCreate($params){
        $filed = "a.id as votes_id,a.vote_name,b.id as meeting_info_id,c.id as meeting_record_id,b.title";
        $meeting = Db::name("meeting_votes")
            ->field($filed)
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

    private function whetherRecordExist($voteName){
        $condition = [
            "meeting_record_id" => $this->meeting["meeting_record_id"],
            "type"              => $this->params["issue_short_name"],
            "user_id"           =>Request::instance()->userId,
            "meeting_info_id"   => $this->params["issue_id"],
            "vote_name"         => $voteName
        ];
        $record = Db::name("user_votes")->where($condition)->count();
        if($record){
            return true;
        }
        return false;
    }

    public function findUserVote($params){
        $vote = Db::name("user_votes")->where($params)->find();
        if($vote){
            return true;
        }
        return false;
    }
}
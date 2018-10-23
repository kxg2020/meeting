<?php
namespace app\index\model;
use app\index\service\Singleton;
use app\index\service\Tool;
use think\Db;

class MeetingVotes extends Base{
    use Singleton;

    /*
     * ĞÂÔöÍ¶Æ±
     */
    public function createVote($params,$meetingInfoId){
        $insert = [
            "options" => Tool::getInstance()->jsonEncode($params["votes"]),
            "meeting_info_id" => $meetingInfoId
        ];
        $result = Db::name("meeting_vote")->insertGetId($insert);
        if($result){
            return $this->returnResponse();
        }
        return $this->returnResponse();
    }
}
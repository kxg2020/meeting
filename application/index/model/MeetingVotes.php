<?php
namespace app\index\model;
use app\index\service\Singleton;
use think\Db;

class MeetingVotes extends Base{
    use Singleton;
    /*
     * Í¶Æ±ÁĞ±í
     */
    public function votesList($meetingInfoId){
        $votes = Db::name("meeting_votes")
            ->where(["meeting_info_id" => $meetingInfoId])
            ->select();
        if($votes){
            return $this->returnResponse($votes);
        }
        return $this->returnResponse();
    }
}
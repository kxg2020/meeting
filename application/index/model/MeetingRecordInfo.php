<?php
namespace app\index\model;
use app\index\service\Enum;
use app\index\service\MeetingVotes;
use app\index\service\Singleton;
use app\index\service\Tool;
use think\Cache;
use think\Db;
use think\facade\Request;

class MeetingRecordInfo extends Base{
    use Singleton;
    private $imageExt = ["jpg,jpeg,png,gif"];
    private $issueDetail;

    public function meetingIssueInfo($issueId){
        $filed = "b.name,b.short_name,a.title,a.file_id,a.content,a.id,a.meeting_record_id as record_id,";
        $filed.= "a.type,c.meeting_info_id,c.options,c.vote_number_limit";
        $this->issueDetail = Db::name("meeting_record_info")
            ->alias("a")
            ->field($filed)
            ->leftJoin("meeting_political b","a.type = b.id")
            ->leftJoin("meeting_vote c","c.meeting_info_id = a.id")
            ->where(["a.id" => $issueId])
            ->find();
        $meetingInfo = $this->singleIssueInfo($issueId);
        return $this->returnResponse($meetingInfo["data"]);
    }

    private function singleIssueInfo($issueId){
        $result = [];
        // 锟斤拷锟斤拷锟斤拷锟斤拷
        $issueDetail = $this->issueDetail;
        $issueDetail["options"] = Tool::getInstance()->jsonDecode($issueDetail["options"]);
        // 当前议题
        $currentIssueStatus = Db::name("user_votes")
            ->where(["meeting_info_id"=>$issueId,"user_id"=>Request::instance()->userId])
            ->find();
        // 会议记录
        $meetingRecord = Db::name("meeting_record")
            ->field("start_time,end_time")
            ->where(["id"=>$issueDetail["record_id"]])
            ->find();
        $editable = true;
        // 会议是否开始
        if($meetingRecord["start_time"] > time()){
            $editable = false;
        }
        // 会议是否过期
        if($meetingRecord["end_time"] < time()){
            $editable = false;
        }
        // 是否已经记录过
        if($currentIssueStatus){
            $editable = false;
        }

        $finishRate = $this->finishRate($issueDetail);
        $result["edit"] = $editable;
        if($issueDetail){
            $result["content"] = $issueDetail["content"];
            $result["vote_number_limit"] = $issueDetail["vote_number_limit"];
            $result["rate"]    = $finishRate;
            $result["issue_name"] = $issueDetail["title"];
            $result["issue_id"] = $issueId;
            $result["issue_short_name"] = $issueDetail["short_name"];
            $result["images"] = [];
            $result["files"]  = [];
            $result["option"] = [];
            // 锟斤拷询锟斤拷锟斤拷锟斤拷募锟斤拷斜锟?
            if(isset($issueDetail["file_id"]) && !empty($issueDetail["file_id"])){
                // 锟斤拷询锟侥硷拷
                $files = MeetingFile::getInstance()->issueFileList($issueDetail["file_id"]);
                if($files["data"]){
                    foreach($files["data"] as $file){
                        $ext = substr($file["file_name"],strpos($file["file_name"],"."));
                        if(in_array($ext,$this->imageExt)){
                            $result["images"][] = [
                                "file_name" => $file["file_name"],
                                "file_url"  => $file["file_url"],
                            ];
                        }else{
                            $result["files"][] = [
                                "file_name" => $file["file_name"],
                                "file_url"  => $file["file_url"],
                            ];
                        }
                    }
                }
            }
            $result = $this->classify($issueDetail,$result);
            return $this->returnResponse($result);
        }
        return $this->returnResponse();
    }

    private function classify($issueDetail,&$result){
        // 锟矫伙拷投票选锟斤拷
        $userVote = Db::name("user_votes")->field("choose")->where([
            "meeting_record_id" => $issueDetail["record_id"],
            "meeting_info_id"   => $issueDetail["id"],
            "user_id"           => Request::instance()->userId
        ])->find();
        $votes = $issueDetail["options"];
        $userVote = Tool::getInstance()->jsonDecode($userVote["choose"]);
        $function = function ($result) use ($votes,$userVote){
            // 遍历筛选被投中选项
            if($votes){
                foreach($votes as $key => &$value){
                    foreach ($value["items"] as $index => &$item){
                        if(isset($userVote[$key]) && in_array($index,$userVote[$key])){
                            $item["selected"] = true;
                        }else{
                            $item["selected"] = false;
                        }
                    }
                }
                unset($item);
                unset($value);
                $result["option"] = $votes;
            }else{
                $result["option"] = [];
            }
            return $result;
        };
        switch($issueDetail["short_name"]){
            case Enum::READ:
                $result["option"] = [];
                break;
            case Enum::BALLOT:
                $result =  $function($result);
                break;
            case Enum::VOTE:
                $result =  $function($result);
                break;
        }
        return $result;
    }

    private function finishRate($issueDetail){
        // 锟斤拷锟斤拷锟斤拷锟斤拷
        $issueNumber = Db::name("meeting_record_info")
            ->where(["meeting_record_id" => $issueDetail["record_id"]])
            ->count();
        // 已经完成
        $finishNumber= Db::name("user_votes")
            ->where(["meeting_record_id" => $issueDetail["record_id"]])
            ->count();
        // 占比
        $finishRate  = sprintf("%.1f",$finishNumber / $issueNumber);
        return $finishRate;
    }
}
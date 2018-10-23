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

    public function meetingIssueInfo($issueId){
        $meetingInfo = Tool::getInstance()->jsonDecode(\think\facade\Cache::get("$issueId-issue-detail"));
        if(!$meetingInfo){
            $meetingInfo = $this->singleIssueInfo($issueId);
            return $this->returnResponse($meetingInfo["data"]);
        }else{
            return $this->returnResponse($meetingInfo);
        }
    }

    private function singleIssueInfo($issueId){
        $result = [];
        $filed = "b.name,b.short_name,a.title,a.file_id,a.content,a.id,a.meeting_record_id as record_id,";
        $filed.= "a.type,c.meeting_info_id,c.options";

        // ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½
        $issueDetail = Db::name("meeting_record_info")
            ->alias("a")
            ->field($filed)
            ->leftJoin("meeting_political b","a.type = b.id")
            ->leftJoin("meeting_vote c","c.meeting_info_id = a.id")
            ->where(["a.id" => $issueId])
            ->find();
        $issueDetail["options"] = Tool::getInstance()->jsonDecode($issueDetail["options"]);
        // ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½
        $issueNumber = Db::name("meeting_record_info")
            ->where(["meeting_record_id" => $issueDetail["record_id"]])
            ->count();

        // ÒÑ¾­Íê³É
        $finishNumber= Db::name("user_votes")
            ->where(["meeting_record_id" => $issueDetail["record_id"]])
            ->count();

        // Õ¼±È
        $finishRate  = sprintf("%.1f",$finishNumber / $issueNumber);

        // µ±Ç°ÒéÌâ
        $currentIssueStatus = Db::name("user_votes")->where(["meeting_info_id"=>$issueId])->find();
        $currentIssueStatus ? $result["edit"] = false :$result["edit"] = true;
        if($issueDetail){
            $result["content"] = $issueDetail["content"];
            $result["rate"]    = $finishRate;
            $result["issue_name"] = $issueDetail["title"];
            $result["issue_id"] = $issueDetail["meeting_info_id"];
            $result["issue_short_name"] = $issueDetail["short_name"];
            $result["images"] = [];
            $result["files"]  = [];
            $result["option"] = [];
            // ï¿½ï¿½Ñ¯ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½Ä¼ï¿½ï¿½Ð±ï¿?
            if(isset($issueDetail["file_id"]) && !empty($issueDetail["file_id"])){
                // ï¿½ï¿½Ñ¯ï¿½Ä¼ï¿½
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
            // ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½
            \think\facade\Cache::set("$issueId-issue-detail",Tool::getInstance()->jsonEncode($result));
            return $this->returnResponse($result);
        }
        return $this->returnResponse();
    }

    private function classify($issueDetail,&$result){
        // ï¿½Ã»ï¿½Í¶Æ±Ñ¡ï¿½ï¿½
        $userVote = Db::name("user_votes")->field("choose")->where([
            "meeting_record_id" => $issueDetail["record_id"],
            "meeting_info_id"   => $issueDetail["id"],
        ])->find();
        $votes = $issueDetail["options"];
        $userVote = Tool::getInstance()->jsonDecode($userVote["choose"]);
        $function = function ($result) use ($votes,$userVote){
            // ±éÀúÉ¸Ñ¡±»Í¶ÖÐÑ¡Ïî
            if($votes){
                foreach($votes as $key => &$value){
                    foreach ($value["items"] as $index => &$item){
                        if($index === $userVote[$key]){
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
}
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

        // 议题详情
        $issueDetail = Db::name("meeting_record_info")
            ->alias("a")
            ->field($filed)
            ->leftJoin("meeting_political b","a.type = b.id")
            ->leftJoin("meeting_vote c","c.meeting_info_id = a.id")
            ->where(["a.id" => $issueId])
            ->find();
        $issueDetail["options"] = Tool::getInstance()->jsonDecode($issueDetail["options"]);
        // 议题数量
        $issueNumber = Db::name("meeting_record_info")
            ->where(["meeting_record_id" => $issueDetail["record_id"]])
            ->count();
        // 已经完成
        $finishNumber= Db::name("user_votes")
            ->where(["meeting_record_id" => $issueDetail["record_id"]])
            ->count();

        // 占比
        $finishRate  = sprintf("%.1f",$finishNumber / $issueNumber);
        // 当前议题
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
            // 查询议题的文件列表
            if(isset($issueDetail["file_id"]) && !empty($issueDetail["file_id"])){
                // 查询文件
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
            // 缓存议题
            \think\facade\Cache::set("$issueId-issue-detail",Tool::getInstance()->jsonEncode($result));
            return $this->returnResponse($result);
        }
        return $this->returnResponse();
    }

    private function classify($issueDetail,&$result){
        // 用户投票选项
        $userVote = Db::name("user_votes")->field("choose")->where([
            "meeting_record_id" => $issueDetail["record_id"],
            "meeting_info_id"   => $issueDetail["id"],
        ])->find();
        $votes = $issueDetail["options"];
        $userVote = Tool::getInstance()->jsonDecode($userVote["choose"]);

        $function = function ($result,$type) use ($votes,$userVote){
            // 遍历筛选被投中选项
            if($votes){
                if($type == Enum::BALLOT){
                    if(isset($votes["items"]) && !empty($votes["items"])){
                        foreach ($votes["items"] as $key => &$value){
                            if($key == $userVote[$key]){
                                $value["selected"] = true;
                            }else{
                                $value["selected"] = false;
                            }
                        }
                    }
                    unset($value);
                    $result["option"] = $votes;
                }else{
                    if(isset($votes["items"]) && !empty($votes["items"])){
                        foreach ($votes["items"] as $key => &$value){
                            foreach ($value as $index => $item){
                                if($index == $userVote[$key]){
                                    $value["selected"] = true;
                                }else{
                                    $value["selected"] = false;
                                }
                            }
                        }
                    }
                    unset($value);
                    $result["option"] = $votes;
                }
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
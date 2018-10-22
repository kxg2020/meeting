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
        $filed = "b.name,b.short_name,a.title,a.file_id,a.content,a.id,a.meeting_record_id as record_id";

        // 议题详情
        $issueDetail = Db::name("meeting_record_info")
            ->alias("a")
            ->field($filed)
            ->leftJoin("meeting_political b","a.type = b.id")
            ->where(["a.id" => $issueId])
            ->find();

        // 议题数量
        $issueNumber = Db::name("meeting_record_info")
            ->where(["meeting_record_id" => $issueDetail["record_id"]])
            ->count();

        // 用户参加的议题数量
        $joinedIssueNumber = Db::name("user_votes")
            ->where([
                "user_id"           => Request::instance()->userId,
                "type"              => $issueDetail["short_name"],
                "issue_name"        => $issueDetail["title"],
                "meeting_record_id" => $issueDetail["record_id"],
            ])->count();
        // 占比
        $alreadyFinishRate = ceil($joinedIssueNumber / $issueNumber);
        if($issueDetail){
            $result["content"] = $issueDetail["content"];
            $result["rate"]    = $alreadyFinishRate;
            $result["issue_name"] = $issueDetail["title"];
            $result["issue_id"] = $issueDetail["id"];
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
            $result = $this->classify($issueId,$issueDetail,$result);
            // 缓存议题
            \think\facade\Cache::set("$issueId-issue-detail",Tool::getInstance()->jsonEncode($result));
            return $this->returnResponse($result);
        }
        return $this->returnResponse();
    }

    private function classify($issueId,$issueDetail,&$result){

        $votes = \app\index\model\MeetingVotes::getInstance()->votesList($issueId);
        $function = function ($result,$type) use ($votes){
            $voteTitle    = [];
            $subjectIndex = 0;

            if(isset($votes["data"]) && !empty($votes["data"])){
                foreach ($votes["data"] as $index => $item){
                    $file = MeetingFile::getInstance()->votesFileList($item["file_id"]);
                    $userVoteCondition = [
                        "vote_id"   => $item["id"],
                        "choose"    => $type == Enum::BALLOT ? 0 : $item["id"],
                        "user_id"   => Request::instance()->userId,
                        "type"      => $type
                    ];

                    $userVote = UserVotes::getInstance()->findUserVote($userVoteCondition);
                    if(in_array($item["vote_name"],$voteTitle)){
                        $result["option"][$subjectIndex]["options"][] = [
                            "selected"    => $userVote,
                            "title"       => $item["vote_name"],
                            "choose_id"   => $item["id"],
                            "choose_name" => $item["vote_choose"],
                            "file"        => $file["data"],
                        ];
                    }else{
                        $voteLength = array_push($voteTitle,$item["vote_name"]);
                        $subjectIndex = $voteLength - 1;
                        $result["option"][$subjectIndex]["options"][] = [
                            "selected"    => $userVote,
                            "title"       => $item["vote_name"],
                            "choose_id"   => $item["id"],
                            "choose_name" => $item["vote_choose"],
                            "file"        => $file["data"],
                        ];
                    }
                }
            }else{
                $result["option"] = [];
            }
            return $result;
        };

        if($votes){
            switch($issueDetail["short_name"]){
                case Enum::READ:
                    $result["option"] = [];
                    break;
                case Enum::BALLOT:
                   $result =  $function($result,Enum::BALLOT);
                    break;
                case Enum::VOTE:
                    $result =  $function($result,Enum::VOTE);
                    break;
            }

        }else{
            $result["votes"] = [];
        }
        return $result;
    }
}
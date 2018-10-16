<?php
namespace app\index\model;
use app\index\service\Enum;
use app\index\service\MeetingVotes;
use app\index\service\Singleton;
use app\index\service\Tool;
use think\Cache;
use think\Db;

class MeetingRecordInfo extends Base{
    use Singleton;
    private $imageExt = ["jpg,jpeg,png,gif"];

    public function meetingIssueInfo($issueId){
        $meetingInfo = $this->singleIssueInfo($issueId);
        return $this->returnResponse($meetingInfo["data"]);
    }

    private function singleIssueInfo($issueId){
        $result = [];
        // 议题详情
        $issueDetail = Db::name("meeting_record_info")
            ->alias("a")
            ->field("b.name,b.short_name,a.title,a.file_id,a.content")
            ->leftJoin("meeting_political b","a.type = b.id")
            ->where(["a.id" => $issueId])
            ->find();
        if($issueDetail){
            $result["content"] = $issueDetail["content"];
            $result["issue_name"] = $issueDetail["title"];
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
                                "file_url"  => $file["url"],
                            ];
                        }else{
                            $result["files"][] = [
                                "file_name" => $file["file_name"],
                                "file_url"  => $file["url"],
                            ];
                        }
                    }
                    $result = $this->classify($issueId,$issueDetail,$result);
                    return $this->returnResponse($result);
                }
            }
            return $this->returnResponse($result);
        }
        return $this->returnResponse();
    }

    private function classify($issueId,$issueDetail,&$result){
        // 投票选项
        $votes = \app\index\model\MeetingVotes::getInstance()->votesList($issueId);
        $function = function ($result) use ($votes){
            $voteTitle    = [];
            $subjectIndex = 0;
            if(isset($votes["data"]) && !empty($votes["data"])){
                foreach ($votes["data"] as $index => $item){
                    $file = MeetingFile::getInstance()->votesFileList($item["file_id"]);
                    if(in_array($item["vote_name"],$voteTitle)){
                        $result["option"][$subjectIndex]["options"][] = [
                            "title"       => $item["vote_name"],
                            "choose_name" => $item["vote_choose"],
                            "file"        => $file["data"]
                        ];
                    }else{
                        $voteLength = array_push($voteTitle,$item["vote_name"]);
                        $subjectIndex = $voteLength - 1;
                        $result["option"][$subjectIndex]["options"][] = [
                            "title"       => $item["vote_name"],
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
                   $result =  $function($result);
                    break;
                case Enum::VOTE:
                    $result =  $function($result);
                    break;
            }

        }else{
            $result["votes"] = [];
        }
        return $result;
    }
}
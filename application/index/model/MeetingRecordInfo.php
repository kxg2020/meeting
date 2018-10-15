<?php
namespace app\index\model;
use app\index\service\Enum;
use app\index\service\MeetingVotes;
use app\index\service\Singleton;
use think\Cache;
use think\Db;

class MeetingRecordInfo extends Base{
    use Singleton;
    private $imageExt = ["jpg,jpeg,png,gif"];

    public function meetingIssueInfo($issueId){
        $meetingInfo = \think\facade\Cache::get("$issueId-info");
        if($meetingInfo){
            $meetingInfo = \app\index\service\Tool::getInstance()
                ->jsonDecode($meetingInfo);
            unset($meetingInfo["data"]["join_user"]);
        }else{
            $meetingInfo = $this->singleIssueInfo($issueId);
            unset($meetingInfo["data"]["join_user"]);
        }

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
            // 查询议题的文件列表
            if(isset($issueDetail["file_id"]) && !empty($issueDetail["file_id"])){
                // 查询文件
                $files = MeetingFile::getInstance()->issueFileList($issueDetail["file_id"]);
                if($files["data"]){
                    $result["images"] = [];
                    $result["files"]  = [];
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
                    // 投票选项
                    $votes = \app\index\model\MeetingVotes::getInstance()->votesList($issueId);


                    if($votes){
                        // 判断议题类型
                        switch($issueDetail["short_name"]){
                            case Enum::READ:
                                $result["votes"] = [];
                                break;
                            case Enum::BALLOT:
                                if(isset($votes["data"]) && !empty($votes["data"])){
                                    array_walk($votes["data"],function($value) use (&$result){
                                        $ballotFile = MeetingFile::getInstance()
                                            ->votesFileList($value["file_id"]);
                                        $result["option"][] = [
                                            "vote_name" => $value["vote_name"],
                                            "options"   => explode(",",$value["vote_choose"]),
                                            "file"      => $ballotFile
                                        ];
                                    });
                                }
                                break;
                            case Enum::VOTE:
                                if(isset($votes["data"]) && !empty($votes["data"])){
                                    foreach($votes["data"] as $index => $item){
                                        // 查询投票文件
                                        $voteFile = MeetingFile::getInstance()
                                            ->votesFileList($item["file_id"]);
                                       
                                    }
                                }else{
                                    $result["option"] = [];
                                }
                                var_dump($result);die;
                                break;
                        }

                    }else{
                        $result["votes"] = [];
                    }
                }
            }else{
                $result["image"] = [];
                $result["file"]  = [];
            }
        }
        return $this->returnResponse();
    }
}
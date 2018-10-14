<?php
namespace app\index\model;

use app\index\service\Singleton;
use think\Cache;
use think\Db;

class MeetingRecordInfo extends Base{
    use Singleton;

    public function meetingIssueInfo($meetingId){
        $meetingInfo = \think\facade\Cache::get("$meetingId-info");
        if($meetingInfo){
            $meetingInfo = \app\index\service\Tool::getInstance()
                ->jsonDecode($meetingInfo);
            unset($meetingInfo["data"]["join_user"]);
        }else{
            $meetingInfo = \app\index\model\MeetingRecord::getInstance()->singleMeetingInfo($meetingId);
            unset($meetingInfo["data"]["join_user"]);
        }
        return $this->ifIssueExist($meetingInfo);
    }


    private function ifIssueExist(&$meetingInfo){
        if($meetingInfo["data"]["issue"]){
            foreach($meetingInfo["data"]["issue"] as $key => $value){
                // 查询单个议题的主体文件
                $singleMainFileId = Db::name("meeting_record_info")
                    ->field("file_id")
                    ->where(["id" => $value["issue_id"]])
                    ->find();
                if($singleMainFileId){
                    $singleMainFile = Db::name("meeting_file")
                        ->where("id","in",$singleMainFileId["file_id"])
                        ->select();
                    $images = [];
                    $file   = [];
                    if($singleMainFile){
                        foreach($singleMainFile as $i => $j){
                            $ext = substr($j["url"],strpos(".",$j["url"]));
                            if($ext == "jpg" || $ext == "jpeg" || $ext == "png" || $ext == "gif"){
                                $images[] = [
                                    "file_name" => $j["file_name"],
                                    "file_url"  => $j["url"]
                                ];
                            }else{

                                $file[]  = [
                                    "file_name" => $j["file_name"],
                                    "file_url"  => $j["url"]
                                ];
                            }
                        }
                    }
                    $meetingInfo["issue_list"][$key] = [
                        "image"=> $images,
                        "file" => $file,
                        "title"=> $value["title"],
                        "vote" => [],
                    ];
                    // 查询单个议题的所有小节
                    $singleIssueChild = Db::name("meeting_votes")
                        ->where(["meeting_info_id"=>$value["issue_id"]])
                        ->select();
                    if($singleIssueChild){
                        foreach($singleIssueChild as $index => $item){
                            $file = Db::name("meeting_file")
                                ->where("id","in",$item["file_id"])
                                ->select();
                            $meetingInfo["issue_list"][$key]["vote"][] = [
                                "file"   => $file,
                                "option" => $item["vote_choose"],
                            ];
                        }
                    }else{
                        $meetingInfo["issue_list"][$key]["vote"] = [];
                    }
                }else{
                    $meetingInfo["issue_list"][$key] = [
                        "file" => [],
                        "vote" => [],
                    ];
                }

            }
            unset($meetingInfo["data"]["issue"]);

            $finalResult = [
                "meetingId"    => $meetingInfo["data"]["meetingId"],
                "meetingTitle" => $meetingInfo["data"]["meetingTitle"],
                "start_time"   => $meetingInfo["data"]["start_time"],
                "end_time"     => $meetingInfo["data"]["end_time"],
                "create_user"  => $meetingInfo["data"]["create_user"],
            ];
            $finalResult["issue_list"] = $meetingInfo["issue_list"];
            return $this->returnResponse($finalResult);
        }
        return $this->returnResponse([],4006);
    }

}
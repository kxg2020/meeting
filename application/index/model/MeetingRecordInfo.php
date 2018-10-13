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
        if($meetingInfo["data"]["issue"]){
            array_walk($meetingInfo["data"]["issue"],function(&$value){
                // 查询议题
                $issue = Db::name("meeting_record_info")
                    ->field("file_id")
                    ->where(["id" =>$value["issue_id"]])
                    ->find();

                if($issue){
                    // 议题文件
                    $fileId = explode(",",$issue["file_id"]);
                    if($fileId){
                        foreach($fileId as $k => $v){
                            // 查询议题的文件
                            $file = Db::name("meeting_file")
                                ->field("url,file_name")
                                ->where(["id" => $v])
                                ->find();
                            $value["issue_file"][] = $file;
                        }
                    }else{
                        $value["issue_file"] = [];
                    }
                    // 议题投票,表决选项
                    $option = Db::name("meeting_votes")
                        ->where(["meeting_info_id" => $value["issue_id"]])
                        ->select();
                    $options = [];
                    $files   = [];
                    if($option){
                        foreach($option as $i => $j){
                            // 查询选项的文件
                            $file = Db::name("meeting_file")
                                ->field("file_name,url")
                                ->where("id","in",$j["file_id"])
                                ->select();
                            $files = $file;
                            // 单个选项
                            $options[] = [
                                "file" => $files,
                                "option"=>$j["vote_choose"]
                            ];
                        }
                    }
                    $value["issue_option"]  = $options;
                }
            });
            return $this->returnResponse($meetingInfo);
        }
        return $this->returnResponse([],4006);
    }
}
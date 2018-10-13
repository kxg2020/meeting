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
                // ��ѯ����
                $issue = Db::name("meeting_record_info")
                    ->field("file_id")
                    ->where(["id" =>$value["issue_id"]])
                    ->find();

                if($issue){
                    // �����ļ�
                    $fileId = explode(",",$issue["file_id"]);
                    if($fileId){
                        foreach($fileId as $k => $v){
                            // ��ѯ������ļ�
                            $file = Db::name("meeting_file")
                                ->field("url,file_name")
                                ->where(["id" => $v])
                                ->find();
                            $value["issue_file"][] = $file;
                        }
                    }else{
                        $value["issue_file"] = [];
                    }
                    // ����ͶƱ,���ѡ��
                    $option = Db::name("meeting_votes")
                        ->where(["meeting_info_id" => $value["issue_id"]])
                        ->select();
                    $files = $optionsName = [];
                    if($option){
                        foreach($option as $index => $item){
                            $optionsName[] = $item["vote_choose"];
                            // ��ѯѡ����ļ�
                            $file = Db::name("meeting_file")
                                ->field("file_name,url")
                                ->where("id","in",$item["file_id"])
                                ->select();
                            $files[$item["vote_choose"]] = $file;
                        }
                    }
                    $value["issue_option"]["file"]    = $files;
                    $value["issue_option"]["option"]  = $optionsName;
                }
            });
            return $this->returnResponse($meetingInfo);
        }
        return $this->returnResponse([],4006);
    }
}
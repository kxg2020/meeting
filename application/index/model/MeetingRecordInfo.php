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
        $filed.= "a.type,c.*";

        // ��������
        $issueDetail = Db::name("meeting_record_info")
            ->alias("a")
            ->field($filed)
            ->leftJoin("meeting_political b","a.type = b.id")
            ->leftJoin("meeting_vote c","c.meeting_info_id = a.id")
            ->where(["a.id" => $issueId])
            ->find();
        $issueDetail["options"] = Tool::getInstance()->jsonDecode($issueDetail["options"]);
        // ��������
        $issueNumber = Db::name("meeting_record_info")
            ->where(["meeting_record_id" => $issueDetail["record_id"]])
            ->count();
        // �Ѿ����
        $finishNumber= Db::name("user_votes")
            ->where(["meeting_record_id" => $issueDetail["record_id"]])
            ->count();
        // ռ��
        $finishRate  = ceil($finishNumber / $issueNumber);
        // ��ǰ����
        $currentIssueStatus = Db::name("user_votes")->where(["meeting_info_id"=>$issueId])->find();
        $currentIssueStatus ? $result["edit"] = false :$result["edit"] = true;
        if($issueDetail){
            $result["content"] = $issueDetail["content"];
            $result["rate"]    = $finishRate;
            $result["issue_name"] = $issueDetail["title"];
            $result["issue_id"] = $issueDetail["id"];
            $result["issue_short_name"] = $issueDetail["short_name"];
            $result["images"] = [];
            $result["files"]  = [];
            $result["option"] = [];
            // ��ѯ������ļ��б�
            if(isset($issueDetail["file_id"]) && !empty($issueDetail["file_id"])){
                // ��ѯ�ļ�
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
            // ��������
            \think\facade\Cache::set("$issueId-issue-detail",Tool::getInstance()->jsonEncode($result));
            return $this->returnResponse($result);
        }
        return $this->returnResponse();
    }

    private function classify($issueDetail,&$result){
        // �û�ͶƱѡ��
        $userVote = Db::name("user_votes")->field("choose")->where([
            "meeting_record_id" => $issueDetail["record_id"],
            "meeting_info_id"   => $issueDetail["id"],
        ])->find();
        $votes = $issueDetail["options"];
        $userVote = Tool::getInstance()->jsonDecode($userVote["choose"]);
        $function = function ($result) use ($votes,$userVote){
            // ����ɸѡ��Ͷ��ѡ��
            if($votes){
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
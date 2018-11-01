<?php
namespace app\index\controller;

use app\index\model\MeetingType;
use app\index\model\UserMeeting;
use app\index\service\Enum;
use app\index\service\Jwt;
use app\index\service\Tool;
use Mpdf\Mpdf;
use Mpdf\MpdfException;
use think\Db;
use think\Exception;
use think\facade\Request;
use think\facade\Url;

class MeetingRecord extends Base {

    /*
     * �����б�������
     */
    public function meetingCreate(){
        $params = Tool::getInstance()->jsonDecode(file_get_contents("php://input"));
        $result = \app\index\model\MeetingRecord::getInstance()
            ->createMeetingRecord($params);
        return $this->printResponse($result["code"]);
    }

    /*
     * ɾ������
     */
    public function meetingDelete(){
        $meetingId = Request::param("meetingId");
        $userRole = \app\index\model\User::getInstance()->getUserByUserId(Request::instance()->userId);
        if(!empty($userRole["data"]["department"])){
            if($userRole["position"] == Enum::ADMIN){
                $result = \app\index\model\MeetingRecord::getInstance()->meetingDelete($meetingId);
                if($result){
                    return $this->printResponse(9004);
                }
                return $this->printResponse(4007);
            }
            return $this->printResponse(4004);
        }
        return $this->printResponse(4004);
    }

    /*
     *ĳ�ֻ���Ļ����б�
     */
    public function meetingRecordList(){
        $typeId = Request::param("typeId");
        $records = \app\index\model\MeetingRecord::getInstance()->getMeetingRecords($typeId, $this->page, $this->pageSize);
        if($records["status"]){
            return $this->printResponse(200, $records["data"]);
        }
        return $this->printResponse();
    }

    /*
     * ��������������б�
     */
    public function singleMeetingInfo(){
        $userId    = request()->userId;
        $meetingId = Request::param("meetingId");
        $result = UserMeeting::getInstance()->userMeetingRecord($meetingId,$userId);
        if(!$result["data"]){
            UserMeeting::getInstance()->createUserMeetingMap($userId,$meetingId);
        }
       $result = \app\index\model\MeetingRecord::getInstance()
            ->singleMeetingInfo($meetingId);
        if($result["data"]){
            return $this->printResponse(200,$result["data"]);
        }
        return $this->printResponse();
    }

    /*
     * �����¼
     */
    public function meetingRecordWord(){
        $meetingId = Request::get("meetingId");
        $filed = "a.title as meetingName,b.title as meetingIssueName,a.create_time as meetingCreateTime";
        $filed.= ",c.name as createUser,d.name as issueType,d.short_name,a.invitation_department_id,";
        $filed.= "b.id as issue_id,a.id as record_id,a.end_time as meetingEndTime";
        $result = [];
        // 会议详情
        $meetingInfo = Db::name("meeting_record")
            ->alias("a")
            ->field($filed)
            ->leftJoin("meeting_record_info b","a.id = b.meeting_record_id")
            ->leftJoin("user c","a.create_user_id = c.id")
            ->leftJoin("meeting_political d","b.type = d.id")
            ->where(["a.id" => $meetingId])
            ->select();
        $token  =  Request::get("token");
        $result = Jwt::getInstance()->validateToken("user_id",$token);
        if($result["status"]){
            Request::instance()->userId = $result["claim"];
        }else{
            Request::instance()->userId = "";
        }
        // 是否是管理员
        $user = Db::name("user")->where(["user_id"=>Request::instance()->userId])->find();
        if(!$user && !$user["position"] == Enum::ADMIN){
            return;
        }

        // 会议是否能导出
        if(!isset($meetingInfo[0]["meetingEndTime"]) || $meetingInfo[0]["meetingEndTime"] > time()){
            return;
        }
        // 会议是否能导出
        if(!isset($meetingInfo[0]["meetingCreateTime"]) || $meetingInfo[0]["meetingCreateTime"] > time()){
            return;
        }

        // 参会人员
        $joinedUser = \app\index\model\Department::getInstance()
            ->departmentMember($meetingInfo[0]["invitation_department_id"]);
        if(!empty($joinedUser["data"])){
            foreach ($joinedUser["data"] as $key => $value){
                $result["shouldJoinUser"][] = $value["name"];
            }
        }else{
            $result["shouldJoinUser"] = [];
        }
        // 实际参会人员
        $realJoinUser = Db::name("user_meeting")
            ->alias("a")
            ->field("b.name")
            ->leftJoin("user b","a.user_id = b.user_id")
            ->where(["a.meeting_record_id"=> $meetingId,"status"=>1])
            ->select();
        if(!empty($realJoinUser)){
            foreach ($realJoinUser as $key => $value){
                $result["realJoinUser"][] = $value["name"];
            }

        }else{
            $result["realJoinUser"] = [];
        }

        $result["shouldJoinUser"] = implode(",",$result["shouldJoinUser"]);
        $result["realJoinUser"]   = implode(",",$result["realJoinUser"]);

        if($meetingInfo){
            foreach ($meetingInfo as $key => $value){
                $result["meetingName"]     = $value["meetingName"];
                $result["meetingCreateTime"]     = date("Y-m-d",$value["meetingCreateTime"]);
                $result["create_user"]     = $value["createUser"];
                $result["meeting_issue"][] = [
                    "meetingIssueName" => $value['meetingIssueName'],
                    "issueType"        => $value["issueType"],
                    "short_name"       => $value["short_name"],
                    "issue_id"         => $value["issue_id"],
                    "record_id"        => $value["record_id"],
                ];
            }
        }else{
            $result["meetingIssue"] = [];
        }
        $result["meeting_info"] = [];
        // 议题详情
        foreach ($result["meeting_issue"] as $key => $value){
            $meetingIssueList = [];
            switch ($value["short_name"]){
                case Enum::READ:
                    $issueInfo = Db::name("meeting_record_info")
                        ->alias("a")
                        ->field("a.content,a.file_id,a.title")
                        ->where(["a.id"=>$value["issue_id"]])
                        ->find();
                    $file = Db::name("meeting_file")
                        ->where("id","in",$issueInfo["file_id"])
                        ->select();
                    // 阅读人员
                    $readUser = Db::name("user_votes")
                        ->alias("a")
                        ->field("b.name")
                        ->leftJoin("user b","a.user_id = b.user_id")
                        ->where([
                            "meeting_record_id"=>$value["record_id"],
                            "meeting_info_id"  =>$value["issue_id"],
                        ])
                        ->select();
                    $readUserName = [];
                    if($readUser){
                        foreach ($readUser as $index => $item){
                            $readUserName[] = $item["name"];
                        }
                        $readUserName = implode(",",$readUserName);
                    }else{
                        $readUserName = "";
                    }
                    $meetingIssueList = [
                        "title"   => $issueInfo["title"],
                        "type"    => Enum::READ,
                        "content" => $issueInfo["content"],
                        "file"    => $file,
                        "read_user" => $readUserName,
                    ];
                    $result["meeting_info"][] = $meetingIssueList;

                    break;
                case Enum::BALLOT:
                    $ballotInfo = Db::name("meeting_record_info")
                        ->alias("a")
                        ->field("a.content,a.file_id,a.title")
                        ->where(["a.id"=>$value["issue_id"]])
                        ->find();
                    $file = Db::name("meeting_file")
                        ->where("id","in",$ballotInfo["file_id"])
                        ->select();
                    // 表决选项
                    $vote = Db::name("meeting_vote")
                        ->where(["meeting_info_id"=>$value["issue_id"]])
                        ->find();
                    $vote["options"] = Tool::getInstance()->jsonDecode($vote["options"]);
                    // 用户表决
                    $userBallot = Db::name("user_votes")
                        ->where(["meeting_info_id"=> $vote["meeting_info_id"],"type"=>Enum::BALLOT])
                        ->select();

                    if($vote["options"]){
                        foreach ($vote["options"] as $i => $j){
                            $agree  = 0;
                            $oppose = 0;
                            $giveUp = 0;
                            foreach ($userBallot as $n => $q){
                                $choose = Tool::getInstance()->jsonDecode($q["choose"]);
                                if(in_array(0,$choose[$i])){
                                    $agree  += 1;
                                }elseif(in_array(1,$choose[$i])){
                                    $oppose += 1;
                                }else{
                                    $giveUp += 1;
                                }
                            }
                            $ballotResult = "";
                            if($agree > $oppose){
                                $ballotResult = "同意";
                            }else if($agree == $oppose){
                                $ballotResult = "票数相同";
                            }else if ($agree < $oppose){
                                $ballotResult = "反对";
                            }
                            $issueOption[$i] = [
                                "title"  => $j["title"],
                                "agree"  => $agree,
                                "oppose" => $oppose,
                                "give_up"=> $giveUp,
                                "result" => $ballotResult ,
                            ];
                        }
                    }else{
                        $issueOption = [];
                    }
                    $meetingIssueList = [
                        "title"     => $ballotInfo["title"],
                        "type"      => Enum::BALLOT,
                        "content"   => $ballotInfo["content"],
                        "file"      => $file,
                        "options"   => $issueOption
                    ];
                    $result["meeting_info"][] = $meetingIssueList;
                    // 表决结果
                    break;
                case Enum::VOTE:
                    $voteInfo = Db::name("meeting_record_info")
                        ->alias("a")
                        ->field("a.content,a.file_id,a.title")
                        ->where(["a.id"=>$value["issue_id"]])
                        ->find();
                    $file = Db::name("meeting_file")
                        ->where("id","in",$voteInfo["file_id"])
                        ->select();
                    // 投票选项
                    $vote = Db::name("meeting_vote")
                        ->where(["meeting_info_id"=>$value["issue_id"]])
                        ->find();
                    $vote["options"] = Tool::getInstance()->jsonDecode($vote["options"]);
                    // 用户投票
                    $userVote = Db::name("user_votes")
                        ->where(["meeting_info_id"=> $vote["meeting_info_id"],"type"=>Enum::VOTE])
                        ->select();

                    if($vote["options"]){
                        foreach ($vote["options"] as $i => $j){
                            foreach ($j["items"] as $k => $v){
                                $agree  = 0;
                                foreach ($userVote as $o => $p){
                                    $choose = Tool::getInstance()->jsonDecode($p["choose"]);
                                    if(in_array($k,$choose[$i])){
                                        $agree  += 1;
                                    }
                                }
                                $itemVote[$k] = [
                                    "option" => $v["value"],
                                    "agree"  => $agree,
                                    "file"   => $v["files"],
                                ];
                            }
                        }
                    }else{
                        $itemVote = [];
                    }
                    if($itemVote){
                        $volume = [];
                        foreach ($itemVote as $u => $i){
                            $volume[$u] = $i["agree"];
                        }
                        array_multisort($volume, SORT_DESC, $itemVote);
                    }
                    $voteResult = isset($itemVote[0]["option"]) ? $itemVote[0]["option"] : "错误";
                    $meetingIssueList = [
                        "title"     => $voteInfo["title"],
                        "type"      => Enum::VOTE,
                        "content"   => $voteInfo["content"],
                        "file"      => $file,
                        "result"    => $voteResult,
                        "options"   => $itemVote
                    ];
                    $result["meeting_info"][] = $meetingIssueList;
                    break;
            }
        }
        $this->assign(["meeting" => $result]);
        $html = $this->fetch("meeting/word");
        echo $html;
        $fileName = "中共白朝乡月坝村党支部党员大会会议记录";
//        try{
//            $pdf = new Mpdf(['default_font' => 'GB','format' => 'A4-L']);
//            $pdf->setFooter('{PAGENO}');
//            $pdf->use_kwt = true;
//            $pdf->autoScriptToLang = true;
//            $pdf->WriteHTML($html);
//            $pdf->Output("$fileName.pdf","D");
//        }catch (MpdfException $exception){
//            echo $exception->getMessage();
//        }
    }
}
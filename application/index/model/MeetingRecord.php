<?php
namespace app\index\model;
use app\index\service\Enum;
use app\index\service\Singleton;
use app\index\service\template\AgentMessageFacade;
use app\index\service\WeChat;
use think\Db;
use think\facade\Cache;


class MeetingRecord extends Base{
    use Singleton;
    private $commit = true;

    public function getMeetingRecords($type,$params){
        $records = Db::name("meeting_record")
            ->where(["meeting_type_id"=>$type])
            ->limit($this->formatLimit($params["pgNum"],$params["pgSize"]),$params["pgSize"])
            ->select();
        $total   = Db::name("meeting_record")
            ->where(["meeting_type_id"=>$type])->count();
        $recordType = Db::name("meeting_type")->where(["id"=>$type])->find();
        $result = [
            "meetingRecords"=> $records,
            "meetingType"   => $recordType,
            "total"         => $total
        ];
        return $this->returnResponse($result);
    }

    public function createMeetingRecord($params){
        // 创建人
        $createUserId = request()->userId;
        // 创建人
        $createUser   = User::getInstance()->getUserByUserId($createUserId);
        $params["host"] = $createUser["data"]["name"];
        // 会议类型
        $meetingType  = MeetingType::getInstance()->getMeetingTypeById($params["meeting_type_id"]);
        // 创建人是否有权限
        if(!substr_count($createUser["data"]["enable_sponsored_meeting_type_id"],$params["meeting_type_id"])){
            return $this->returnResponse([],4001);
        }
        // 插入会议记录表
        $insertMeetingRecord = [
            "title"           => $params["title"],
            "create_user_id"  => $createUser["data"]["id"],
            "create_time"     => time(),
            "meeting_type_id" => $meetingType["data"]["id"],
            "start_time"      => $params["start_time"],
            "end_time"        => $params["end_time"],
            "invitation_department_id" => $params["user_invitation_id"],
        ];
        Db::startTrans();
        $resultRecord = Db::name("meeting_record")->insertGetId($insertMeetingRecord);
        if($resultRecord){
            $departmentRealId = Department::getInstance()->departmentRealId($params["user_invitation_id"]);
            $params["department_real_id"] = $departmentRealId["data"];
            if(isset($params["issue_list"]) && $params["issue_list"]){
                foreach($params["issue_list"] as $key => $value){
                    $this->meetingInsert($value,$resultRecord,$params);
                }
            }
        }
        if(\app\index\service\Format::getInstance()->commit){
            // 消息模板
            $template = AgentMessageFacade::TextCard()->setParams($params)->templateInit()->fillTemplateValue();
            // 发送应用消息
            WeChat::getInstance()->setPost($template)->sendAgentMessage();

            Db::commit();
            return $this->returnResponse([],9002);
        }
        Db::rollback();
        return $this->returnResponse([],4002);
    }

    private function meetingInsert($value,$resultRecord,$params){
        $insertMeetingInfo = [
            "meeting_record_id" => $resultRecord,
            "type"        => $value["political_id"],
            "create_time" => time(),
            "title"       => $value["title"]
        ];
        \app\index\service\Format::getInstance()
            ->fileContainerFlush()
            ->setMeetingTypeValue($value)
            ->meetingTypeDispatch($insertMeetingInfo,$params);
    }

    public function singleMeetingInfo($meetingRecordId){
        $result = [];
        $field = "c.title as typeTitle,b.*,d.short_name,a.title as meetingTitle";
        $field.= ",e.name,a.start_time,a.end_time,a.invitation_department_id,";
        $field.= "a.id as meetingId,b.id as issue_id,d.name as political_name";
        $meetingIssue = Db::name("meeting_record")
            ->alias("a")
            ->field($field)
            ->leftJoin("meeting_record_info b","a.id = b.meeting_record_id")
            ->leftJoin("meeting_type c","a.meeting_type_id = c.id")
            ->leftJoin("meeting_political d","b.type = d.id")
            ->leftJoin("user e","a.create_user_id = e.id")
            ->where(["a.id" => $meetingRecordId])
            ->select();
        if($meetingIssue){
           $result["meetingId"] = $meetingIssue[0]["meetingId"];
            $this->classifyMeetingUser($result,$meetingRecordId,$meetingIssue);
            foreach($meetingIssue as $key => $value){
                $result["meetingTitle"] = $value["meetingTitle"];
                $result["start_time"]   = date("Y-m-d H:i:s",$value["start_time"]);
                $result["end_time"]     = date("Y-m-d H:i:s",$value["end_time"]);
                $result["create_user"]  = $value["name"];
                $result["issue"][] = [
                    "issue_id" => $value["issue_id"],
                    "title" => $value["title"],
                    "political_name" => $value["political_name"],
                    "political_type" => $value["short_name"],
                ];
            }

            $meetingInfo = $this->returnResponse($result);
            // 缓存会议
            Cache::set("$meetingRecordId-info",\app\index\service\Tool::getInstance()->jsonEncode($meetingInfo));
           return $meetingInfo;
        }
        return $this->returnResponse();
    }

    private function classifyMeetingUser(&$result,$meetingRecordId,$meetingIssue){
        $alreadyJoinedUserName = [];
        $allUserInDatabase = User::getInstance()->allUserInDatabase("avatar,user_id");
        $allUserAvatar = [];
        if($allUserInDatabase["data"]){
            array_walk($allUserInDatabase["data"],function($value) use (&$allUserAvatar){
                $allUserAvatar[$value["user_id"]] = $value["avatar"];
            });
        }
        $alreadyJoinedUser = UserMeeting::getInstance()
            ->meetingJoinUser($meetingRecordId);
        if($alreadyJoinedUser["data"]){
            foreach($alreadyJoinedUser["data"] as $value){
                $alreadyJoinedUserName[] = $value["name"];
            }
        }
        $notJoinedYetUser = Department::getInstance()
            ->departmentMember($meetingIssue[0]["invitation_department_id"]);

        if($notJoinedYetUser["data"]){
            foreach($notJoinedYetUser["data"] as $value){
                if(in_array($value["name"],$alreadyJoinedUserName)){
                    $result["join_user"][] = [
                        "is_joined" => true,
                        "avatar"    => $allUserAvatar[$value["userid"]],
                        "username"  => $value["name"],
                    ];
                }else{
                    $result["join_user"][] = [
                        "is_joined" => false,
                        "avatar"    => $allUserAvatar[$value["userid"]],
                        "username"  => $value["name"],
                    ];
                }
            }
        }
    }
}
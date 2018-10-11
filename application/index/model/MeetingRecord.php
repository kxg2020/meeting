<?php
namespace app\index\model;

use app\index\service\Enum;
use app\index\service\Singleton;
use think\Db;
use think\facade\Log;


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
        // 会议类型
        $meetingType  = MeetingType::getInstance()->getMeetingTypeById($params["meeting_type_id"]);
        Log::write($createUser["data"]["enable_sponsored_meeting_type_id"]);
        Log::write($params["meeting_type_id"]);
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
        ];
        Db::startTrans();
        $resultRecord = Db::name("meeting_record")->insertGetId($insertMeetingRecord);
        if($resultRecord){
            if(isset($params["issue_list"]) && $params["issue_list"]){
                foreach($params["issue_list"] as $key => $value){
                    $this->meetingInsert($value,$resultRecord,$params);
                }
            }
        }
        if(\app\index\service\Format::getInstance()->commit){
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
    

}
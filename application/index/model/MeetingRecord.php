<?php
namespace app\index\model;

use app\index\service\Enum;
use app\index\service\Singleton;
use think\Db;


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
        var_dump($params);die;
        // 创建人
        $createUserId = request()->userId;
        // 创建人
        $createUser   = User::getInstance()->getUserByUserId($createUserId);
        // 会议类型
        $meetingType  = MeetingType::getInstance()->getMeetingTypeById($params["meeting_type"]);
        // 创建人是否有权限
        if(!strpos($meetingType["data"]["permission"],$createUser["data"]["id"])){
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
                $this->commit ? Db::commit() : Db::rollback();
            }
        }
        $this->returnResponse([],4001);
    }

    private function meetingInsert($value,$resultRecord,$params){
        $insertMeetingInfo = [
            "meeting_record_id" => $resultRecord,
            "type"        => $value["political_id"],
            "create_time" => time(),
        ];
        \app\index\service\Format::getInstance()
            ->fileContainerFlush()
            ->setMeetingTypeValue($value)
            ->meetingTypeDispatch($insertMeetingInfo,$params);

        if(\app\index\service\Format::getInstance()->commit){
            Db::commit();return;
        }
        Db::rollback();
    }

    private function insertReadMeeting(){

    }


}
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
        Db::startTrans();
        // 插入会议记录表
        $insertMeetingRecord = [
            "title"           => $params["title"],
            "create_user_id"  => $createUser["data"]["id"],
            "create_time"     => time(),
            "meeting_type_id" => $meetingType["data"]["id"],
            "start_time"      => $params["start_time"],
            "end_time"        => $params["end_time"],
        ];
        $resultRecord = Db::name("meeting_record")->insertGetId($insertMeetingRecord);
        if($resultRecord){
            foreach($params["issue_list"] as $key => $value){
                if($value["political_id"] == Enum::READ){
                    $this->meetingRead($value,$resultRecord,$params);
                }
                if($value["political_id"] == Enum::BALLOT){
                    $this->meetingBallot($value,$resultRecord,$params);
                }
                if($value["political_id"] == Enum::VOTES){
                    $this->meetingVotes($value,$resultRecord,$params);
                }
            }
           $this->commit ? Db::commit() : Db::rollback();
        }
        $this->returnResponse([],4001);
    }

    private function meetingRead($value,$resultRecord,$params){
        $fileId = [];
        $insertMeetingInfo = [
            "meeting_record_id" => $resultRecord,
            "type" => $value["political_id"],
            "create_time" => time(),
        ];
        $insertMeetingInfo =  \app\index\service\Format::getInstance()
            ->setMeetingType($value["political_id"])
            ->meetingFormat($insertMeetingInfo,$params);
        if(isset($value["images"]) && $value["images"]){
            foreach($value["images"] as $k => $v){
                $fileId[] = $this->createFile($v);
            }
        }
        if(isset($value["files"]) && $value["files"]){
            foreach($value["images"] as $k => $v){
                $fileId[] = $this->createFile($v);
            }
        }
        $insertMeetingInfo["file_id"] = implode("|",$fileId);
        $result = Db::name("meeting_record")->insert($insertMeetingInfo);
        $result ? : $this->commit = false;
    }

    private function meetingBallot($value,$resultRecord,$params){

    }

    private function meetingVotes($value,$resultRecord,$params){

    }


    private function createFile($params){
        $insert = [
            "url" => $params["fil_url"],
            "file_name" => $params["fil_name"],
        ];
        $result = Db::name("meeting_file")->insertGetId($insert);
        if($result){
             return $result;
        }
        $this->commit = false;
        return false;
    }
}
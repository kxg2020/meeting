<?php
namespace app\index\model;

use app\index\service\Singleton;
use think\Db;


class MeetingRecord extends Base{
    use Singleton;

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
        ];
        $resultRecord = Db::name("meeting_record")->insertGetId($insertMeetingRecord);
        if($resultRecord){
            $insertMeetingInfo = [];
            foreach($params["issue_list"] as $key => $value){
                $insertMeetingInfo = [
                    "meeting_record_id" => $resultRecord,
                ];
               $insertMeetingInfo =  \app\index\service\Format::getInstance()
                    ->setMeetingType($value["political_id"])
                    ->meetingFormat($insertMeetingInfo);

            }
        }
        $this->returnResponse([],4001);
    }



}
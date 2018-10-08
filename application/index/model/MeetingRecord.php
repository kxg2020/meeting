<?php
namespace app\index\model;

use app\index\service\Singleton;
use think\Db;


class MeetingRecord extends Base{
    use Singleton;

    public function getMeetingRecords($type){
        $records = Db::name("meeting_record")->where(["meeting_type_id"=>$type])->select();
        $recordType = Db::name("meeting_type")->where(["id"=>$type])->find();
        $result = [
            "meetingRecords" => $records,
            "meetingType"   => $recordType
        ];
        return $this->returnResponse($result);

    }
}
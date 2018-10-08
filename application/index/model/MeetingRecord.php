<?php
namespace app\index\model;

use app\index\service\Singleton;
use think\Db;


class MeetingRecord extends Base{
    use Singleton;

    public function getMeetingRecords($type)
    {
        $field = "a.id,a.title,a.create_time,b.title as meetingType,b.id as meetingTypeId";
        $records = Db::name('meeting_record')
            ->alias("a")
            ->leftJoin("meeting_type b", "a.meeting_type_id = b.id")
            ->field($field)
            ->where(['a.meeting_type_id' => $type])
            ->select();
        return $this->returnResponse($records);

    }
}
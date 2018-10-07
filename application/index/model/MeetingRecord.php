<?php
namespace app\index\model;

use app\index\service\Singleton;
use think\Db;

class MeetingRecord extends Base
{
    use Singleton;

    public function getMeetingRecords($type){
        $meeting_records = Db::name('meeting_record')
            ->field('id,title,create_time')
            ->where(['meeting_type_id' => $type])
            ->select();
        $meeting_type = Db::name('meeting_type')->where('id', $type)->find();
        return $this->returnResponse([
            'meeting_records' => $meeting_records,
            'meeting_type' => $meeting_type
        ]);
    }
}
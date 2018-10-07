<?php
namespace app\index\model;

use app\index\service\Singleton;
use think\Db;

class MeetingRecord extends Base
{
    use Singleton;

    public function getMeetingRecords($type){
        $records = Db::name('meeting_record')
            ->field('id,title,create_time')
            ->where(['meeting_type_id' => $type])
            ->select();
        return $this->returnResponse($records);
    }
}
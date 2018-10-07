<?php
/**
 * Created by PhpStorm.
 * User: gaow
 * Date: 2018/10/7
 * Time: 20:48
 */

namespace app\index\model;


use think\Db;

class MeetingRecord extends Base
{
    use Singleton;

    public function getMeetingRecords($meeting_type_id)
    {
        return Db::name('meeting_record')
            ->field('id,title,create_time')
            ->where(['meeting_type_id' => $meeting_type_id])
            ->select();
    }
}
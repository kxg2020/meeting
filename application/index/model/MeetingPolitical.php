<?php
namespace app\index\model;

use app\index\service\Singleton;
use think\Db;

class MeetingPolitical extends Base{
    use Singleton;

    public function getPoliticalList(){
        $result = Db::name("meeting_political")->where(["status"=>1])->select();
        return $this->returnResponse($result);
    }
}
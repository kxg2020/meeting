<?php
namespace app\index\model;
use app\index\service\Singleton;
use think\Db;

class MeetingType extends Base{
    use Singleton;
    /*
     * all
     */
    public function meetingTypes(){
        $field  = "id,title,img_url";
        $result = Db::name("meeting_type")
            ->field($field)->select();
        return $this->returnResponse($result);
    }


    /*
     * ��������
     */
    private function condition($params){
        $where = [
            ["status","=", 1]
        ];
        if(isset($params["key"])){
            $where[] = ["title","like","%".$params["key"]."%"];
        }
        return $where;
    }
}
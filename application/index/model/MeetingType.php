<?php
namespace app\index\model;
use app\index\service\Singleton;
use think\Db;

class MeetingType extends Base{
    use Singleton;
    /*
     * ��������
     */
    public function meetingType($params){
        $field  = "id,title,img_url";
        $result = Db::name("meeting_type")
            ->field($field)->where($this->condition($params))->select();
        return $this->returnResponse($result);
    }

    /*
     * ����type_id��ȡ�б�
     */
    public function meetingList($meetingTypeId){
        $result = Db::name("");
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
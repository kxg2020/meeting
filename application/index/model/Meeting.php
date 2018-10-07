<?php
namespace app\index\model;
use app\index\service\Singleton;
use think\Db;

class Meeting extends Base{
    use Singleton;
    /*
     * ��������
     */
    public function meetingType(){
        $field  = "id,title,img_url";
        $result = Db::name("meeting")->field($field)->where(["status" => 1])->select();
        return $this->returnResponse($result);
    }

    /*
     * �����б�
     */
    public function meetingList($meetingTypeId){
        $result = Db::name("");
    }
}
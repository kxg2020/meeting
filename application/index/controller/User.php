<?php
namespace app\index\controller;

use app\index\service\Singleton;
use think\Db;

class User extends Base{
    use Singleton;

    /*
     * ��ѯ�û�
     */
    public function getUserByUserId($username){
        $user = Db::name("user")->where(["username"=>$username])->find();
        return $this->printResponse(200,$user);
    }

    /*
     * ����û�
     */
    public function createUser(array $user){
        $insert = [
            "user_id"   => $user["UserId"],
            "device_id" => isset($user["DeviceId"]) ? $user["DeviceId"] : "",
            "user_tickt"=> isset($user["user_ticket"]) ? $user["user_ticket"] : "",
        ];
        $result = Db::name("user")->insertGetId($insert);
        return $this->printResponse(200,$result);
    }
}
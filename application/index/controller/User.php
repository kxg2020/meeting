<?php
namespace app\index\controller;
use app\index\service\Singleton;
use think\Db;

class User extends Base{
    use Singleton;
    private $userInfo;

    /*
     * 查询用户
     */
    public function getUserByUserId($username){
        $user = Db::name("user")->where(["username"=>$username])->find();
        return $this->printResponse(200,$user);
    }

    /*
     * 添加用户
     */
    public function createUser(){
        $result = Db::name("user")->insertGetId($this->userInfo);
        return $this->printResponse(200,$result);
    }

    /*
     * 用户详情
     */
    public function setUserInfo($params = []){
        $this->userInfo = [
            "user_id" => $params["userid"],
            "name"    => $params["name"],
            "department" => json_encode($params["department"]),
            "avatar"  => $params["avatar"],
            "mobile"  => $params["mobile"],
            "enable"  => $params["enable"],
            "position"=> $params["position"],
        ];
        return $this;
    }

}
<?php
namespace app\index\controller;
use app\index\model\User;
use app\index\service\Aes;
use app\index\service\WeChat;
use think\Exception;
use think\facade\Config;
use think\facade\Request;


class Index extends Base{

    public function index(){
        return view('index');
        $code = Request::get("code");
        if($code){
            $userBasic = WeChat::getInstance()->setCode($code)->getUserBasic();
            if($userBasic){
                // 获取成员部门
                $user = User::getInstance()->getUserByUserId($userBasic["UserId"]);
                if(!$user["data"]){
                    $userInfo = WeChat::getInstance()
                        ->getUserInfo($userBasic["UserId"]);
                    User::getInstance()->setUserInfo($userInfo)->createUser();
                }
            }
        }else{
            // 授权接口
            $redirect = sprintf($this->authApi,$this->companyId,$this->redirect,$this->agentId);
            return redirect($redirect);
        }
        // 返回token
        header("token",(new Aes(Config::get("aes_key")))->encrypt($userBasic["UserId"]));
       return view('index');
    }





}

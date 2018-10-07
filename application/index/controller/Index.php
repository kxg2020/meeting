<?php
namespace app\index\controller;

use app\index\service\WeChat;
use think\Exception;
use think\facade\Request;


class Index extends Base{

    public function index(){
        return view('index');
        $code = Request::get("code");
        if($code){
            $userInfo = WeChat::getInstance()->setCode($code)->getMemberInfo();
            if($userInfo){
                $user = User::getInstance()->getUserByUserId($userInfo["UserId"]);
                if(!$user["data"]){
                    User::getInstance()->createUser($userInfo);
                }
            }
        }else{
            // 授权接口
            $redirect = sprintf($this->authApi,$this->companyId,$this->redirect,$this->agentId);
            return redirect($redirect);

        }
       return view('index');
    }





}

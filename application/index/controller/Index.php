<?php
namespace app\index\controller;
use app\index\model\Department;
use app\index\model\User;
use app\index\service\Aes;
use app\index\service\Jwt;
use app\index\service\WeChat;
use think\Exception;
use think\facade\Config;
use think\facade\Log;
use think\facade\Request;
use think\facade\Url;


class Index extends Base{

    /*
     * �û���½
     */
    public function index(){
        // dev
        if (strpos(Request::domain(), 'localhost')){
            return view('index', [
                'token' => Jwt::getInstance()->createToken("user_id", "whngqdcmhdxxf"),
                'permission_ids' => [87, 88]
            ]);
        }

        $code = Request::get("code");
        if($code){
            $userBasic = WeChat::getInstance()->setCode($code)->getUserBasic();
            $userInfo  = WeChat::getInstance()->getUserInfo($userBasic["UserId"]);
            if($userBasic){
                $user = User::getInstance()->getUserByUserId($userBasic["UserId"]);
                if(!$user["data"]){
                    User::getInstance()->setUserInfo($userInfo)->createUser()->updatePermission($userInfo);
                }else{
                    User::getInstance()->setUserInfo($userInfo)->updateUser($userBasic["UserId"])->updatePermission($userInfo);
                }
            }else{
                $this->redirect = Request::url(true);
                $redirect = sprintf($this->authApi,$this->companyId,$this->redirect,$this->agentId);
                header(sprintf("Location: %s;",$redirect));
            }
        }else{
            $redirect = sprintf($this->authApi,$this->companyId,$this->redirect,$this->agentId);
            header(sprintf("Location: %s;",$redirect));
        }
        $viewPermission = Department::getInstance()->loginUserViewPermission($userInfo["department"]);
        $viewPermissionId = [];
        if($viewPermission["data"]){
            array_walk($viewPermission["data"],function ($value) use (&$viewPermissionId){
                $viewPermissionId[] = $value["meetingTypeId"];
            });
        }
       return view('index', [
           'token' => Jwt::getInstance()->createToken("user_id",$userBasic["UserId"]),
           "permission_ids"  => $viewPermissionId,
       ]);
    }

}

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
use think\facade\Response;
use think\facade\Url;
use think\response\Redirect;


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
        $ifRedirect = Request::get("redirect");
        if($code){
            $userBasic = WeChat::getInstance()->setCode($code)->getUserBasic();
            $userInfo  = WeChat::getInstance()->getUserInfo($userBasic["UserId"]);
            $viewPermission = Department::getInstance()->loginUserViewPermission($userInfo["department"]);
            $viewPermissionId = [];
            if($viewPermission["data"]){
                array_walk($viewPermission["data"],function ($value) use (&$viewPermissionId){
                    $viewPermissionId[] = $value["meetingTypeId"];
                });
            }
            if($userBasic){
                $user = User::getInstance()->getUserByUserId($userBasic["UserId"]);
                if(!$user["data"]){
                    User::getInstance()->setUserInfo($userInfo)->createUser()->updatePermission($userInfo);
                }else{
                    User::getInstance()->setUserInfo($userInfo)->updateUser($userBasic["UserId"])->updatePermission($userInfo);
                }
                return view('index', [
                    'token'    => Jwt::getInstance()->createToken("user_id",$userBasic["UserId"]),
                    "permission_ids"  => $viewPermissionId,
                    "redirect" => $ifRedirect
                ]);
            }else{
                $this->redirect = Request::url(true);
                $redirect = sprintf($this->authApi,$this->companyId,$this->redirect,$this->agentId);
                return \redirect($redirect);
            }
        }else{
            $redirect = sprintf($this->authApi,$this->companyId,$this->redirect,$this->agentId);
            return \redirect($redirect);
        }

    }

}

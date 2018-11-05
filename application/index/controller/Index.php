<?php
namespace app\index\controller;
use app\index\model\Department;
use app\index\model\User;
use app\index\service\Jwt;
use app\index\service\Tool;
use app\index\service\WeChat;
use think\facade\Request;



class Index extends Base{

    /*
     * �û���½
     */
    public function index(){
        // dev
        if (strpos(Request::domain(), 'localhost')){
            return view('index', [
                'token' => Jwt::getInstance()->createToken("user_id", "whngqdcmhdxxf"),
                'permission_ids' => [87, 88],
                "route_path" => '',
                "user_info" => [
                    "name" => 'name',
                    "position" => 'position',
                    "avatar" => 'https://img.it9g.com/other/FvO_Csuv2DyvYZxzc97xjxLWyoeO.jpeg'
                ]
            ]);
        }
        $code = Request::get("code");
        $ifRedirect = Request::get("redirect") ? Request::get("redirect") : "";
        $this->redirect = urlencode($this->redirect.'?redirect='.$ifRedirect);
        if($code){
            $userBasic = WeChat::getInstance()->setCode($code)->getUserBasic();
            $userInfo  = User::getInstance()->getUserByUserId($userBasic["UserId"]);

            $userInfo["data"]["department"] = Tool::getInstance()->jsonDecode($userInfo["data"]["department"]);
            $viewPermission = Department::getInstance()
                ->loginUserViewPermission($userInfo["data"]["department"],$userInfo);
            $viewPermissionId = [];
            if($viewPermission["data"]){
                array_walk($viewPermission["data"],function ($value) use (&$viewPermissionId){
                    $viewPermissionId[] = $value["meetingTypeId"];
                });
            }
            if($userBasic){
                return view('index', [
                    'token'           => Jwt::getInstance()->createToken("user_id",$userBasic["UserId"]),
                    "permission_ids"  => $viewPermissionId,
                    "route_path" => $ifRedirect,
                    "user_info"  => [
                        "name"   => $userInfo["data"]['name'],
                        "position" => $userInfo["data"]["position"],
                        "avatar"   => $userInfo["data"]['avatar']
                    ]
                ]);
            }else{
                $redirect = sprintf($this->authApi,$this->companyId,$this->redirect,$this->agentId);
                return \redirect($redirect);
            }
        }else{
            $redirect = sprintf($this->authApi,$this->companyId,$this->redirect,$this->agentId);
            return \redirect($redirect);
        }

    }

}

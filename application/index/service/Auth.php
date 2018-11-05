<?php
namespace app\index\service;
use app\index\model\User;
use think\facade\Request;

class Auth{
    private $notice = "当前用户被禁用,无法进行任何操作";
    public function handle($request, \Closure $next){
        $token = Request::header("token",Request::get("token")) ;
        if($token){
            $result = Jwt::getInstance()->validateToken("user_id",$token);
            if($result["status"]){
                $request->userId = $result["claim"];
                $userInfo = User::getInstance()->getUserByUserId($request->userId);
                if($userInfo["data"]["enable"]){
                    return $next($request);
                }else{
                    return json(["msg" => $this->notice,"status" => false,"code" => 204]);
                }
            }else{
                return json(["msg" => $result["msg"],"status" => false,"code" => 500]);
            }
        }else{
            return json(["msg" => "token not exist.","status" => false,"code" => 500]);
        }
    }
}
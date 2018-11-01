<?php
namespace app\index\service;

use think\Exception;
use think\facade\Config;
use think\facade\Request;

class Auth{

    public function handle($request, \Closure $next){
        $token = Request::header("token") ? Request::header("token") : Request::get("token");
        if($token){
            $result = Jwt::getInstance()->validateToken("user_id",$token);
            if($result["status"]){
                $request->userId = $result["claim"];
                return $next($request);
            }else{
                return json(["msg" => $result["msg"],"status" => false,"code" => 500]);
            }
        }else{
            return json(["msg" => "token not exist.","status" => false,"code" => 500]);
        }
    }
}
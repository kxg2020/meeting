<?php
namespace app\index\service;

use think\Exception;
use think\facade\Config;
use think\facade\Request;

class Auth{

    public function handle($request, \Closure $next){
        $token = Request::header("token");
        if($token){
            $userId = Jwt::getInstance()->validateToken("user_id",$token);
            if($userId){
                $request->userId = $userId;
                return $next($request);
            }else{
                return json(["msg" => "token validation failed.","status" => false,"code" => 500]);
            }
        }else{
            return json(["msg" => "token not exist.","status" => false,"code" => 500]);
        }
    }
}
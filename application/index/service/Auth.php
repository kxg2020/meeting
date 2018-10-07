<?php
namespace app\index\service;

use think\Exception;
use think\facade\Config;
use think\facade\Request;

class Auth{

    public function handle($request, \Closure $next){
        $token = Request::header("token");
        if($token){
            $key = (new Aes(Config::get("aes_key")))->decrypt($token);
            if($key == Config::get("aes_key")){
                return $next($request);
            }else{
                return json(["msg" => "token validation failed.","status" => false,"code" => 0]);
            }
        }else{
            return json(["msg" => "token not exist.","status" => false,"code" => 0]);
        }
    }
}
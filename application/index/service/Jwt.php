<?php
namespace app\index\service;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use think\Exception;
use think\facade\Config;
class Jwt{
    use Singleton;
    public function createToken($key,$value){
        $signer = new Sha256();
        $token = (new Builder())
            ->setId('sxs-4f1g23a12aa', true)
            ->setIssuedAt(time())
            ->setExpiration(time() + (86400 * 30))
            ->set($key, $value)
            ->sign($signer, Config::get("token"))
            ->getToken();
        return (string) $token;
    }


    public function validateToken($key,$token = null){
        try{
            $token  = (new Parser())->parse((string) $token);
            $signer = new Sha256();
            if (!$token->verify($signer, Config::get("token"))) {
                return ["status" => false,"msg"=>"token illegal","claim" => ""];
            }
            return ["status" => true,"msg" => "","claim" => $token->getClaim($key)];
        }catch (Exception $e){
            return ["status" => false,"msg"=>$e->getMessage(),"claim" => ""];
        }
    }
}
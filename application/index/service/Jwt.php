<?php
namespace app\index\service;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
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
        $token  = (new Parser())->parse((string) $token);
        $signer = new Sha256();
        if (!$token->verify($signer, Config::get("token"))) {
            return false;
        }
        return $token->getClaim($key);
    }
}
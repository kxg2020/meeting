<?php
namespace app\index\controller;

use think\facade\Log;
use think\facade\Request;

class AddressBook extends Base{
    private $encodeKey = "wqm1uwsSBxBLXE0lo5EUaru7lTUFSSWm0nD1bY3Kbmc";
    private $token     = "AddressBook";

    /*
     * 通讯录变更
     */
    public function addressBookModifiedNotify(){
        Log::error(func_get_args());
        $this->validateToken(Request::get());
    }

    private function validateToken($params){
        $msgSignature  = $params["msg_signature"];
        $timestamp     = $params["timestamp"];
        $nonce         = $params["nonce"];
        $echostr       = $params["echostr"];
        $array = array($nonce, $timestamp, $this->token);
        sort($array);
        $str = sha1( implode($array));
        if($str == $msgSignature && $echostr ){
            echo  $echostr;
            exit;
        }
    }
}
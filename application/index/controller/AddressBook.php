<?php
namespace app\index\controller;

use think\facade\Log;
use think\facade\Request;

class AddressBook extends Base{
    private $encodeKey = "wqm1uwsSBxBLXE0lo5EUaru7lTUFSSWm0nD1bY3Kbmc";
    private $token     = "AddressBook";
    private $validate  = true;

    /*
     * 通讯录变更
     */
    public function addressBookModifiedNotify(){

        $this->validateToken(Request::get());
        if($this->validate === false){

        }
    }

    private function validateToken($params){
        $msgSignature  = urldecode($params["msg_signature"]);
        $timestamp     = urldecode($params["timestamp"]);
        $nonce         = urldecode($params["nonce"]);
        $echoStr       = rawurldecode($params["echostr"]);
        $array = [$nonce,$timestamp,$this->token,$echoStr];
        sort($array,SORT_STRING);
        $str = sha1(implode($array));
        Log::error($str == $msgSignature);
        if($str == $msgSignature){
            echo  $echoStr;
            exit;
        }else{
            $this->validate = false;
        }
    }
}
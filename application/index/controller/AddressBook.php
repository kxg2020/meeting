<?php
namespace app\index\controller;

use think\facade\Log;
use think\facade\Request;

class AddressBook extends Base{
    private $encodeKey = "wqm1uwsSBxBLXE0lo5EUaru7lTUFSSWm0nD1bY3Kbmc";
    private $token     = "AddressBook";
    private $validate  = null;

    /*
     * 通讯录变更
     */
    public function addressBookModifiedNotify(){
        Log::error(func_get_args());
        $this->validateToken(Request::get());
        if($this->validate === false){

        }
    }

    private function validateToken($params){
        $msgSignature  = $params["msg_signature"];
        $timestamp     = $params["timestamp"];
        $nonce         = $params["nonce"];
        $echostr       = $params["echostr"];
        $array = [$nonce,$timestamp,$this->token,$echostr];
        sort($array,SORT_STRING);
        $str = sha1(implode($array));
        if($str == $msgSignature){
            echo  $echostr;
            $this->validate = true;
           exit;
        }else{
            $this->validate = false;
        }
    }
}
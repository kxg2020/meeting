<?php
namespace app\index\controller;

use app\index\service\Aes;
use app\index\service\Pkcs;
use app\index\service\WeChat;
use think\Exception;
use think\facade\Log;
use think\facade\Request;

class AddressBook extends Base{
    private $encodeKey = "wqm1uwsSBxBLXE0lo5EUaru7lTUFSSWm0nD1bY3Kbmc";
    private $token     = "AddressBook";
    private $validate  = true;
    private $iv;
    private $key;

    /*
     * 通讯录变更
     */
    public function addressBookModifiedNotify(){
        $this->validateToken(Request::get());
        if($this->validate === false){

        }
    }

    private function validateToken($params){
        $msgSignature  = rawurldecode($params["msg_signature"]);
        $timestamp     = rawurldecode($params["timestamp"]);
        $nonce         = rawurldecode($params["nonce"]);
        $echoStr       = rawurldecode($params["echostr"]);
        $array = [$nonce,$timestamp,$this->token,$echoStr];
        sort($array,SORT_STRING);
        $str = sha1(implode($array));
        if($str == $msgSignature){
            $this->key = base64_decode($this->encodeKey."=");
            $this->iv  = substr($this->key, 0, 16);
            $result = $this->decrypt($echoStr,WeChat::COMPANY_ID);
            echo $result;exit;
        }else{
            $this->validate = false;
        }
    }

    private function decrypt($encrypted, $receiveId){
        $decrypted = (new Aes($this->key,"AES-256-CBC",$this->iv,OPENSSL_ZERO_PADDING))
            ->decrypt($encrypted);
        $pkc_encoder = new Pkcs();
        $result      = $pkc_encoder->decode($decrypted);
        if (strlen($result) < 16) {
            return [];
        }
        $content     = substr($result, 16, strlen($result));
        $len_list    = unpack('N', substr($content, 0, 4));
        $xml_len     = $len_list[1];
        $xmlContent  = substr($content, 4, $xml_len);
        $from_receiveId = substr($content, $xml_len + 4);
        if($from_receiveId != $receiveId){
            return false;
        }
        return $xmlContent;
    }


}
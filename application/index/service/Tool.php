<?php
namespace app\index\service;

class Tool{
    use Singleton;

    /*
     * jsonDecode
     */
    public function jsonDecode($data){
        return json_decode($data,1);
    }

    /*
     * jsonEncode
     */
    public function jsonEncode($data){
        return json_encode($data,256);
    }


}
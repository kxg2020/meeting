<?php
namespace app\index\model;

use think\Model;

class Base  extends  Model{
    protected $response = [];

    protected  function returnResponse($data = [],$code = 200){
        $this->response = [
            "code"   => $code,
            "data"   => $data,
            "status" => $code == 200 ? true : false
        ];
        return $this->response;
    }

    protected function formatLimit($pgNum,$pgSize){
        $start = ($pgNum - 1) * $pgSize;
        return $start;
    }

}
<?php
namespace app\index\service;
use think\exception\Handle;

class ApiException extends Handle{
    private $exception = [];

    public function render(\Exception $e){
        $this->exception = [
            "status" => 500,
            "msg"    => "接口异常",
            "data"   => []
        ];
        return json($this->exception);
    }


}
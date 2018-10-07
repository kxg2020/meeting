<?php
namespace app\index\service;
use think\exception\Handle;

class ApiException extends Handle{
    private $exception = [];

    public function render(\Exception $e){
        $this->exception = [
            "status" => false,
            "msg"    => "接口异常",
            "data"   => [],
            "code"   => 500
        ];
        return json($this->exception);
    }


}
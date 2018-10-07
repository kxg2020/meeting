<?php
namespace app\index\service;
use think\exception\Handle;
use think\facade\Log;

class ApiException extends Handle{
    private $exception = [];

    public function render(\Exception $e){
        Log::error($e->getMessage());
        $this->exception = [
            "status" => false,
            "msg"    => "接口异常",
            "data"   => [],
            "code"   => 500
        ];
        return json($this->exception);
    }


}
<?php
namespace app\index\service\event;

trait EventTrait{
    public function __construct($data){
        $this->data = $data;
    }
}
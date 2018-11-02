<?php
namespace app\index\service;
use app\index\service\event\InterfaceEvent;

class Event{
    use Singleton;
    private $observe  = [];

    public function addEvent(InterfaceEvent $observe){
        $this->observe[] = $observe;
        return $this;
    }

    public function notify(){
        foreach ($this->observe as $instance){
            $instance->trigger();
        }
    }
}
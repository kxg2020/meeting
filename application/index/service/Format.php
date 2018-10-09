<?php
namespace app\index\service;

class Format{
    use Singleton;
    private $meetingType = [
        1 => "read",
        2 => "ballot",
        3 => "votes"
    ];
    public $meeting;
    public $params;

    public function setMeetingType($type){
        $this->meeting = $this->meetingType[$type];
        return $this;
    }

    public function meetingFormat($params){
        $this->params = $params;
        if(method_exists($this,$this->meeting)){
            return call_user_func([$this,$this->meeting]);
        }
    }

    public function read(){
        $this->params[] = "";
    }

    public function ballot(){

    }

    public function votes(){

    }
}
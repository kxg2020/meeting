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
    public $meetingInfo;

    public function setMeetingType($type){
        $this->meeting = $this->meetingType[$type];
        return $this;
    }

    public function meetingFormat($meetingInfo,$params){
        $this->params = $params;
        $this->meetingInfo = $meetingInfo;
        if(method_exists($this,$this->meeting)){
            return call_user_func([$this,$this->meeting]);
        }
    }

    public function read(){
        $this->meetingInfo["content"] = $this->params["content"];
        return $this->meetingInfo;
    }

    public function ballot(){
        $this->meetingInfo["content"] = "";
        return $this->meetingInfo;
    }

    public function votes(){
        $this->meetingInfo["content"] = "";
        return $this->meetingInfo;
    }
}
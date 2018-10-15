<?php
namespace app\index\service\template;

use app\index\service\Singleton;
use app\index\service\WeChat;

abstract class AgentMessage{
    use Singleton;
    protected $params;
    protected $type = "";
    protected $template;
    protected $btnText = "查看详情";
    protected $title   = "新会议通知";
    protected $redirect= "https://www.baidu.com";

    protected function commonTemplateInit(){
        $this->template = [
            "touser"   => "",
            "msgtype"  => $this->type,
            "toparty"  => $this->params["department_real_id"],
            "totag"    => "",
            "agentid"  => WeChat::AGENT_ID,

        ];
        return $this;
    }

    protected function paramsFormat(){
        $start = date("Y-m-d H:i:s",$this->params["start_time"]);
        $end   = date("Y-m-d H:i:s",$this->params["end_time"]);
        $this->params["start_time"] = $start;
        $this->params["end_time"]   = $end;
    }

    public function setParams(array $params){
        $this->params = $params;
        return $this;
    }

    public function trigger(){
        return $this->template;
    }

    protected function templateTypeInit($key,$value){
        $this->template[$key] = $value;
        return $this;
    }
}
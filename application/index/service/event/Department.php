<?php
namespace app\index\service\event;

use think\Db;
use think\facade\Log;

class Department implements InterfaceEvent {
    private $data;
    private $updateTime;
    use EventTrait;

    public function trigger(){
        $this->updateTime = date("Y-m-d H:i:s",time());
        switch ($this->data["MsgType"]){
            case "event":
                call_user_func([$this,$this->data["ChangeType"]]);
                break;
        }
    }

    private function create_party(){
        $insert = [];
        $insert["create_time"] = time();
        if(isset($this->data["Id"]) && !empty($this->data["Id"])){
            $insert["department_id"] = $this->data["Id"];
        }
        if(isset($this->data["Name"]) && !empty($this->data["Name"])){
            $insert["name"] = $this->data["Name"];
        }
        if(isset($this->data["ParentId"]) && !empty($this->data["ParentId"])){
            $insert["parent_id"] = $this->data["ParentId"];
        }
        $result = Db::name("department")->insertGetId($insert);
        Db::name("meeting_type")->insertGetId([
            "title" => $this->data["Name"]."»áÒé",
            "department_id" => $this->data["Id"],
            "create_time"   => time()
        ]);
        if($result){
            Log::write("[success]:create department {$this->data["Name"]} information success!,time:{$this->updateTime},create department :".print_r($insert,1),"notice");
        }else{
            Log::write("[failed]:create department {$this->data["Name"]} information failed!,time:{$this->updateTime},create department :".print_r($insert,1),"notice");
        }
    }

    private function update_party(){
        $update = [];
        $update["update_time"] = time();
        if(isset($this->data["Id"]) && !empty($this->data["Id"])){
            $update["department_id"] = $this->data["Id"];
        }
        if(isset($this->data["Name"]) && !empty($this->data["Name"])){
            $update["name"] = $this->data["Name"];
        }
        if(isset($this->data["ParentId"]) && !empty($this->data["ParentId"])){
            $update["parent_id"] = $this->data["ParentId"];
        }
        $result = Db::name("department")->where(["department_id"=>$this->data["Id"]])->update($update);
        if($result !== false){
            Log::write("[success]:update department {$this->data["Name"]} information success!,time:{$this->updateTime},update department :".print_r($update,1),"notice");
        }else{
            Log::write("[failed]:update department {$this->data["Name"]} information failed!,time:{$this->updateTime},update department :".print_r($update,1),"notice");
        }
    }

    private function delete_party(){
        $result = Db::name("department")->where(["department_id"=>$this->data["Id"]])->delete();
                 Db::name("meeting_type")->where(["department_id"=>$this->data["Id"]])->delete();
        if($result){
            Log::write("[success]:delete department {$this->data["Name"]} information success!,time:{$this->updateTime},delete department :".print_r($result,1),"notice");
        }else{
            Log::write("[failed]:delete department {$this->data["Name"]} information failed!,time:{$this->updateTime},delete department :".print_r($result,1),"notice");
        }
    }
}
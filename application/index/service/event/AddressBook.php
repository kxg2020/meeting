<?php
namespace app\index\service\event;

use app\index\model\User;
use app\index\service\Tool;
use think\Db;
use think\facade\Cache;
use think\facade\Log;

class AddressBook implements InterfaceEvent{
    private $data;
    private $updateTime;
    use EventTrait;

    public function trigger(){
        $this->updateTime = date("Y-m-d H:i:s",time());
        switch ($this->data["MsgType"]){
            case "event":
                if(method_exists($this,$this->data["ChangeType"]) && is_callable([$this,$this->data["ChangeType"]])){
                    call_user_func([$this,$this->data["ChangeType"]]);
                }
                break;
        }
    }

    /*
     * update user 
     */
    private function update_user(){
        $update = [];
        if(isset($this->data["Position"]) && !empty($this->data["Position"])){
            $update["position"] = $this->data["Position"];
        }
        if(isset($this->data["Department"]) && !empty($this->data["Department"])){
            $update["department"] = Tool::getInstance()->jsonEncode(explode(",",$this->data["Department"]));
        }
        if(isset($this->data["Mobile"]) && !empty($this->data["Mobile"])){
            $update["mobile"]  = $this->data["Mobile"];
        }
        if(isset($this->data["Avatar"]) && !empty($this->data["Avatar"])){
            $update["avatar"]  = str_replace("/0","/100",$this->data["Avatar"]);
        }
        Db::startTrans();
        $upt = Db::name("user")->where(["user_id"=>$this->data["UserID"]])->update($update);
        $upp = true;
        if(isset($this->data["Position"]) && !empty($this->data["Position"])){
            $upp = User::getInstance()->updatePermission($this->data);
        }
        if($upt !== false && $upp !== false){
            Log::write("[success]:update user {$this->data["UserID"]} information success!,time:{$this->updateTime},updated data:".print_r($update,1),"notice");
            Cache::clear();
            Db::commit();
        }else{
            Log::write("[failed]:update user {$this->data["UserID"]} information failed!,time:{$this->updateTime},updated data:".print_r($update,1),"notice");
            Db::rollback();
        }
    }

    /*
     * create user 
     */
    private function create_user(){
        $insert = [];
        if(isset($this->data["UserID"]) && !empty($this->data["UserID"])){
            $insert["user_id"] = $this->data["UserID"];
        }
        if(isset($this->data["Mobile"]) && !empty($this->data["Mobile"])){
            $insert["mobile"]  = $this->data["Mobile"];
        }
        if(isset($this->data["Name"]) && !empty($this->data["Name"])){
            $insert["name"]    = $this->data["Name"];
        }
        if(isset($this->data["Position"]) && !empty($this->data["Position"])){
            $insert["position"]    = $this->data["Position"];
        }
        if(isset($this->data["Department"]) && !empty($this->data["Department"])){
            $insert["department"]  = Tool::getInstance()->jsonEncode(explode(",",$this->data["Department"]));
        }
        if(isset($this->data["Avatar"]) && !empty($this->data["Avatar"])){
            $insert["avatar"]  = str_replace("/0","/100",$this->data["Avatar"]);
        }
        Db::startTrans();
        $ins =  Db::name("user")->insertGetId($insert);
        $upt = true;
        if(isset($this->data["Position"]) && !empty($this->data["Position"])){
            $upt = User::getInstance()->updatePermission($this->data);
        }
        if($ins && $upt !== false){
            Log::write("[success]:create user {$this->data["UserID"]} information success!,time:{$this->updateTime},create user :".print_r($insert,1),"notice");
            Cache::clear();
            Db::commit();
        }else{
            Log::write("[failed]:create user {$this->data["UserID"]} information failed!,time:{$this->updateTime},create user :".print_r($insert,1),"notice");
            Db::rollback();
        }
    }

    /*
     * delete user 
     */
    private function delete_user(){
        $update = ["enable"=>0];
       $result = Db::name("user")->where(["user_id"=>$this->data["UserID"]])->update($update);
       if($result){
           Cache::clear();
           Log::write("[success]:delete user {$this->data["UserID"]} information success!,time:{$this->updateTime}","notice");
       }else{
           Log::write("[failed]:delete user {$this->data["UserID"]} information failed!,time:{$this->updateTime},updated data:".print_r($update,1),"notice");
       }
    }
}
<?php
namespace app\index\service\event;

use app\index\model\User;
use app\index\service\Tool;
use think\Db;
use think\facade\Log;

class AddressBook implements InterfaceEvent{
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

    /*
     * �����û�
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
            Log::write("[success]:�����û�{$this->data["UserID"]}�ĸ�����Ϣ�ɹ�,ʱ��:{$this->updateTime},��������:".print_r($update,1),"notice");
            Db::commit();
        }else{
            Log::write("[failed]:�����û�{$this->data["UserID"]}�ĸ�����Ϣʧ��,ʱ��:{$this->updateTime},��������:".print_r($update,1),"notice");
            Db::rollback();
        }
    }

    /*
     * �����û�
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
            Log::write("[success]:�����û�{$this->data["UserID"]}�ĸ�����Ϣ�ɹ�,ʱ��:{$this->updateTime},��������:".print_r($insert,1),"notice");
            Db::commit();
        }else{
            Log::write("[success]:�����û�{$this->data["UserID"]}�ĸ�����Ϣʧ��,ʱ��:{$this->updateTime},��������:".print_r($insert,1),"notice");
            Db::rollback();
        }
    }

    /*
     * ɾ���û�
     */
    private function delete_user(){
        $update = ["enable"=>0];
       $result = Db::name("user")->where(["user_id"=>$this->data["UserID"]])->update($update);
       if($result){
           Log::write("[success]:ɾ���û�{$this->data["UserID"]}�ĸ�����Ϣ�ɹ�,ʱ��:{$this->updateTime}","notice");
       }else{
           Log::write("[success]:ɾ���û�{$this->data["UserID"]}�ĸ�����Ϣʧ��,ʱ��:{$this->updateTime},��������:".print_r($update,1),"notice");
       }
    }
}
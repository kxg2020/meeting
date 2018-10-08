<?php
namespace app\index\model;
use app\index\service\Singleton;
use app\index\service\Tool;
use app\index\service\WeChat;
use think\Db;
use think\facade\Cache;

class Department extends Base{
    use Singleton;

    public function getDepartmentList(){
        $result = Tool::getInstance()->jsonDecode(Cache::get("department"));
        if($result){
            return $this->returnResponse($result);
        }else{
            $result = WeChat::getInstance()->getDepartmentList();
            if($result){
                Cache::set("department",Tool::getInstance()->jsonEncode($result),86400 * 7);
                $this->createDepartment($result["department"]);
            }
            return $this->returnResponse($result);
        }
    }

    public function createDepartment($params){
        foreach ($this->yieldArray($params,count($params)) as $key => $value){
           $record = Db::name("department")->field("id")->where(["department_id"=>$value["id"]])->find();
           $data   = ["name" => $value["name"],"parent_id" => $value["parentid"],"department_id"=>$value["id"]];
           if($record){
               // ¸üÐÂ
               Db::name("department")->where(["department_id"=>$value["id"]])->update($data);
           }else{
               // ²åÈë
               Db::name("department")->insert($data);
           }
        }
    }

    private function yieldArray($params,$length){
        for($i = 0; $i < $length;$i++){
            yield $params[$i];
        }
    }
}
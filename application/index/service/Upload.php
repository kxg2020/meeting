<?php
namespace app\index\service;

use think\Db;
use think\facade\Request;
use think\facade\Url;

class Upload{
    use Singleton;
    private $result = [];
    private $savePath = "static/uploads";
    private $size   = 1024 * 1024 * 3;

    public function uploadFile(){
        if($this->checkPath()){
            $file = Request::file("image");
            $root = Url::build("index/index");
            $info = $file->validate(['size'=>$this->size,'ext'=>'jpg,png,gif,docx,pptx,xls,pdf,doc'])->move($this->savePath,"");
            if($info){
                $imgUrl = $root."static/uploads/".$info->getSaveName();
                return json(["status" => true,"img_url" =>$imgUrl,"msg"=>"success"]);
            }else{
                return json(["status" => false,"img_url" =>"","msg" => $file->getError()]);
            }
        }
        return json(["status" => false,"img_url" =>"","msg" => "uploads文件夹不存在"]);
    }

    public function checkPath(){
        if(!is_dir($this->savePath)){
          $result = mkdir($this->savePath,0777);
          if($result){
              return true;
          }
          return false;
        }
        return true;
    }
}
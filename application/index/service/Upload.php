<?php
namespace app\index\service;
use think\facade\Request;
use think\facade\Url;

class Upload{
    use Singleton;
    private $result   = [];
    private $savePath = "static/uploads/";
    private $size     = 1024 * 1024 * 20;
    private $extension= 'jpg,png,gif,docx,pptx,xls,xlsx,pdf,doc';

    public function uploadFile(){
        if($this->checkPath()){
            $file = Request::file("file");
            $root = Url::build("index/index");
            $info = $file->validate(['size'=>$this->size,'ext'=>$this->extension])->move($this->savePath,"");
            if($info){
                $this->result["img_url"]   = $root."static/uploads/".$info->getSaveName();
                $this->result["file_name"] = $info->getFilename();
                return json(["status" => true,"data" =>$this->result,"msg"=>"success"]);
            }else{
                return json(["status" => false,"data" =>$this->result,"msg" => $file->getError()]);
            }
        }
        return json(["status" => false,"data" =>$this->result,"msg" => "uploads component not exist."]);
    }

    public function checkPath(){
        $this->savePath .= date("Y-m-d");
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
<?php
namespace app\index\model;

use app\index\service\Singleton;
use think\Db;

class MeetingFile extends Base{
    use Singleton;

    /*
     * 议题文件
     */
    public function issueFileList($fileId){
        $file = Db::name("meeting_file")->where("id","in",$fileId)->select();
        if($file){
            array_walk($file,function (&$value){
                $value["file_url"] = $value["url"];
                unset($value["url"]);
            });
            return $this->returnResponse($file);
        }
        return $this->returnResponse();
    }

    /*
     * 投票文件
     */
    public function votesFileList($fileId){
        return $this->issueFileList($fileId);
    }
}
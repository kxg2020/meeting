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
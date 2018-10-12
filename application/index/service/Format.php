<?php
namespace app\index\service;

use think\Db;
use think\facade\Log;

class Format{
    use Singleton;
    public $meeting;
    public $params;
    public $meetingInfo;
    public $commit = true;
    public $issue;
    public $fileId = [];
    public $meetingRecordId;

    public function setMeetingTypeValue($issue){
        $this->issue   = $issue;
        $this->meeting = Enum::MeetingTypeShortName[$issue["political_short_name"]];
        return $this;
    }

    public function meetingTypeDispatch($meetingInfo,$params){
        $this->params      = $params;
        $this->meetingInfo = $meetingInfo;
        if(method_exists($this,$this->meeting) && is_callable([$this,$this->meeting])){
            return call_user_func([$this,$this->meeting]);
        }
    }

    public function fileContainerFlush(){
        $this->fileId = [];
        return $this;
    }

    private function commonFiles(){
        if(isset($this->issue["images"]) && $this->issue["images"]){
            foreach($this->issue["images"] as $key => $value){
                $this->fileId[] = $this->createFile($value);
            }
        }
        if(isset($this->issue["files"]) && $this->issue["files"]){
            foreach($this->issue["files"] as $key => $value){
                $this->fileId[] = $this->createFile($value);
            }
        }
        $this->meetingInfo["content"] = isset($this->params["content"]) ? $this->params["content"] : "";
        $this->meetingInfo["file_id"] = implode(",",$this->fileId);
    }

    public function read(){
        $this->commonFiles();
        $result = Db::name("meeting_record_info")->insert($this->meetingInfo);
        if(!$result){
            $this->commit = false;
        }
    }

    public function ballot(){
        $this->commonFiles();
        $infoId = Db::name("meeting_record_info")->insertGetId($this->meetingInfo);
        if(isset($this->issue["votes"]) && $this->issue["votes"]){
            foreach($this->issue["votes"] as $key => $value){
                $insertMeetingVote["vote_name"] = $value["title"];
                $insertMeetingVote["meeting_info_id"] = $infoId;
                $votes = [];
                if($value["items"]){
                    foreach($value["items"] as $item){
                        $votes[] = $item["value"];
                    }
                }
                $insertMeetingVote["vote_choose"] = implode(",",$votes);
                $result = Db::name("meeting_votes")->insertGetId($insertMeetingVote);
                if(!$result){
                    $this->commit = false;
                }
            }
        }

    }

    public function votes(){
        $this->commonFiles();
        $infoId = Db::name("meeting_record_info")->insertGetId($this->meetingInfo);
        if(isset($this->issue["votes"]) && $this->issue["votes"]){
            foreach($this->issue["votes"] as $key => $value){
                $insertMeetingVote["meeting_info_id"] = $infoId;
                if($value["items"]){
                    foreach($value["items"] as $item){
                        $fileId = [];
                        $insertMeetingVote["vote_name"] = $item["value"];
                        if(isset($item["files"]) && !empty($item["files"])){
                            foreach($item["files"] as $file){
                              $fileId[] =  $this->createFile($file);
                            }
                        }
                        $singleVoteId = implode(",",$fileId);
                        $insertMeetingVote["vote_choose"] = $item["value"];
                        $insertMeetingVote["file_id"]     = $singleVoteId;
                        $result = Db::name("meeting_votes")->insertGetId($insertMeetingVote);
                        if(!$result){
                            $this->commit = false;
                        }
                    }
                }
            }
        }

    }

    private function createFile($params){
        $insert = [
            "url"       => isset($params["file_url"])  ? $params["file_url"]  : "",
            "file_name" => isset($params["file_name"]) ? $params["file_name"] : "",
        ];
        $result = Db::name("meeting_file")->insertGetId($insert);
        if($result){
            return $result;
        }
        $this->commit = false;
        return false;
    }
}
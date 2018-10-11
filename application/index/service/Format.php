<?php
namespace app\index\service;

use think\Db;

class Format{
    use Singleton;
    private $meetingType = [
        "yz" => "read",
        "bj" => "ballot",
        "tp" => "votes"
    ];
    public $meeting;
    public $params;
    public $meetingInfo;
    public $commit = true;
    public $issue;
    public $fileId = [];

    public function setMeetingTypeValue($issue){
        $this->issue   = $issue;
        $this->meeting = $this->meetingType[$issue["political_short_name"]];
        return $this;
    }

    public function meetingTypeDispatch($meetingInfo,$params){
        $this->params      = $params;
        $this->meetingInfo = $meetingInfo;
        if(method_exists($this,$this->meeting)){
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
        $this->meetingInfo["content"] = $this->params["content"];
        $this->meetingInfo["file_id"] = implode(",",$this->fileId);
    }

    public function read(){
        $this->commonFiles();
        $result = Db::name("meeting_record")->insert($this->meetingInfo);
        if(!$result){
            $this->commit = false;
        }
    }

    public function ballot(){
        $this->commonFiles();
        if(isset($this->issue["votes"]) && $this->issue["votes"]){
            foreach($this->issue["votes"] as $key => $value){
                $insertMeetingVote["vote_name"] = $value["title"];
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
        $result = Db::name("meeting_record")->insert($this->meetingInfo);
        if(!$result){
            $this->commit = false;
        }
    }

    public function votes(){
        $this->commonFiles();
        if(isset($this->issue["votes"]) && $this->issue["votes"]){
            foreach($this->issue["votes"] as $key => $value){
                $insertMeetingVote["vote_name"] = $value["title"];
                $votes = $fileId = [];
                if($value["items"]){
                    foreach($value["items"] as $item){
                        $votes[] = $item["value"];
                        if(isset($item["files"]) && !empty($item["files"])){
                            foreach($item["files"] as $file){
                              $fileId[] =  $this->createFile($file);
                            }
                        }
                    }
                }
                $insertMeetingVote["vote_choose"] = implode(",",$votes);
                $insertMeetingVote["file_id"]     = implode(",",$fileId);
                $result = Db::name("meeting_votes")->insertGetId($insertMeetingVote);
                if(!$result){
                    $this->commit = false;
                }
            }
        }
        $result = Db::name("meeting_record")->insert($this->meetingInfo);
        if(!$result){
            $this->commit = false;
        }

        return $this->meetingInfo;
    }

    private function createFile($params){
        $insert = [
            "url" => $params["fil_url"],
            "file_name" => $params["fil_name"],
        ];
        $result = Db::name("meeting_file")->insertGetId($insert);
        if($result){
            return $result;
        }
        $this->commit = false;
        return false;
    }
}
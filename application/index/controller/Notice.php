<?php
namespace app\index\controller;
use app\index\service\Enum;
use app\index\service\Tool;
use think\facade\Cache;
use think\facade\Request;

class Notice extends Base {

    public function createNotice(){
        $params = Request::post();
        $result = \app\index\model\Notice::getInstance()->createNotice($params);
        if($result){
            return $this->printResponse(9005);
        }
        return $this->printResponse(4009);
    }

    public function deleteNotice(){
        $noticeId = Request::param("noticeId");
        $result   = \app\index\model\Notice::getInstance()->deleteNotice($noticeId);
        if($result){
            return $this->printResponse(9006);
        }
        return $this->printResponse(4010);
    }

    public function detailNotice(){
        $noticeId = Request::param("noticeId");
        $result   = Tool::getInstance()->jsonDecode(Cache::get("notice-".$noticeId));
        if(!$result){
            $result = \app\index\model\Notice::getInstance()->detailNotice($noticeId);
            if($result){
                Cache::set("notice-".$noticeId,Tool::getInstance()->jsonEncode($result));
                return $this->printResponse(200,$result);
            }
            return $this->printResponse();
        }
        return $this->printResponse(200,$result);
    }

    public function noticeList(){
        $result = \app\index\model\Notice::getInstance()->getNoticeList($this->page, $this->pageSize);
        return $this->printResponse(200, $result);
    }

    public function noticeCreatePermission(){
        $userId = Request::instance()->userId;
        $user = \app\index\model\User::getInstance()->getUserByUserId($userId);
        if(!empty($user["data"])){
            if($user["data"]["position"] == Enum::ADMIN){
                return $this->printResponse(200,["create"=>true]);
            }
            return $this->printResponse(200,["create"=>false]);
        }
        return $this->printResponse(200,["create"=>false]);
    }
}
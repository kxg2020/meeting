<?php
namespace app\index\controller;

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
        $result = \app\index\model\Notice::getInstance()->detailNotice($noticeId);
        if($result){
            return $this->printResponse(200,$result);
        }
        return $this->printResponse();
    }

    public function noticeList(){
        $result = \app\index\model\Notice::getInstance()->getNoticeList($this->page, $this->pageSize);
        return $this->printResponse(200, $result);
    }
}
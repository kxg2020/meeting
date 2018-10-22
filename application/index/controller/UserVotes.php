<?php
namespace app\index\controller;

use app\index\service\Tool;

class UserVotes extends Base{

    /*
     * ÓÃ»§Í¶Æ±
     */
    public function createUserVotes(){
        $params = Tool::getInstance()->jsonDecode(file_get_contents("php://input"));
        $result = \app\index\model\UserVotes::getInstance()->createUserVotes($params);
        if($result["status"]){
            return $this->printResponse($result["code"]);
        }
        return $this->printResponse($result["code"]);
    }
}
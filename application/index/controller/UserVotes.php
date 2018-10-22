<?php
namespace app\index\controller;

class UserVotes extends Base{

    /*
     * ำรปงอถฦฑ
     */
    public function createUserVotes(){
        $params = file_get_contents("php://input");
        var_dump($params);
    }
}
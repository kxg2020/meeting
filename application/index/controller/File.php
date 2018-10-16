<?php
namespace app\index\controller;

use app\index\service\Upload;

class File extends Base{

    /*
     * Í¼Æ¬ÉÏ´«
     */
    public function upload(){
       return Upload::getInstance()->uploadFile();
    }
}
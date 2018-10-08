<?php
namespace app\index\controller;

use app\index\service\Upload;

class File extends Base{

    public function upload(){
       return Upload::getInstance()->uploadFile();
    }
}
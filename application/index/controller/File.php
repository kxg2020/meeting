<?php
namespace app\index\controller;

use app\index\service\Upload;

class File extends Base{

    /*
     * ͼƬ�ϴ�
     */
    public function upload(){
       return Upload::getInstance()->uploadFile();
    }
}
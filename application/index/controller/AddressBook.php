<?php
namespace app\index\controller;

use think\facade\Log;

class AddressBook extends Base{
    private $encodeKey = "wqm1uwsSBxBLXE0lo5EUaru7lTUFSSWm0nD1bY3Kbmc";
    private $token     = "AddressBook";

    /*
     * ͨѶ¼���
     */
    public function addressBookModifiedNotify(){
        Log::write(func_get_args());
    }
}
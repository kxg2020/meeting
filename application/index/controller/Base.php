<?php
namespace app\index\controller;
use app\index\service\WeChat;
use think\Controller;

use think\facade\Url;

class Base extends Controller{
    protected $agentId;
    protected $companyId;
    protected $redirect;
    protected $authApi = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=snsapi_base&agentid=%s&state=1#wechat_redirect";
    protected $message = [
        9999 => "测试"
    ];
    private $response = [];


    protected  function initialize(){
        $this->agentId   = WeChat::AGENT_ID;
        $this->companyId = WeChat::COMPANY_ID;
        $this->redirect  = Url::build("index/index");
    }

    protected function printResponse($code = 200,$data = [],$msg = ""){
        $this->response["status"] = $code ? true : false;
        $this->response["data"]   = $data;
        if(isset($this->message[$code])){
            $this->response["msg"]    = $msg ? $msg : $this->message[$code];
        }
        return json($this->response);
    }


}
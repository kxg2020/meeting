<?php
namespace app\index\controller;
use app\index\service\WeChat;
use think\Controller;

use think\facade\Request;
use think\facade\Url;

class Base extends Controller{
    protected $agentId;
    protected $companyId;
    protected $redirect;
    protected $authApi = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=snsapi_base&agentid=%s&state=1#wechat_redirect";
    protected $message = [
        4001 => "没有创建会议权限",
        4002 => "会议创建失败",
        4003 => "用户不存在",
        4004 => "没有权限",
        4005 => "议题不存在",
        4006 => "会议不存在",
        4007 => "会议不存在",
        4008 => "会议删除失败",
        4009 => "新闻创建失败",
        4010 => "新闻删除失败",
        4011 => "会议还未结束,无法导出",
        4012 => "会议还未开始,无法导出",
        4013 => "当前用户不是管理员,无法导出",

        9002 => "会议创建成功",
        9003 => "保存成功",
        9004 => "会议删除成功",
        9005 => "新闻创建成功",
        9006 => "新闻删除成功",
    ];
    private $response = [];
    protected $page = 1;
    protected $pageSize = 10;


    protected  function initialize(){
        $this->agentId   = WeChat::AGENT_ID;
        $this->companyId = WeChat::COMPANY_ID;
        $this->redirect  = Url::build("index/index");
        $this->page = Request::get('page', 1);
        $this->pageSize = Request::get('page_size', 10);
    }

    protected function printResponse($code = 200,$data = [],$msg = ""){
        $this->response["status"] = ($code == 200 || $code > 9000) ? true : false;
        $this->response["data"]   = $data;
        $this->response["msg"]    = $msg;
        if(isset($this->message[$code])){
            $this->response["msg"]    = $msg ? $msg : $this->message[$code];
        }
        return json($this->response);
    }

    /**
     * @param $token
     * @param $user_info
     * @param array $permission_ids
     * @param string $route_path
     * @param string $msg
     * @return \think\response\View
     */
    protected function indexView($token, $user_info, $permission_ids = [], $route_path = '', $msg = ''){
        return view('index', [
            'token' => $token,
            'permission_ids' => $permission_ids,
            "route_path" => $route_path,
            "user_info" => $user_info,
            "msg" => $msg
        ]);
    }
}
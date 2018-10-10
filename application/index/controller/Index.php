<?php
namespace app\index\controller;
use app\index\model\User;
use app\index\service\Aes;
use app\index\service\Jwt;
use app\index\service\WeChat;
use think\Exception;
use think\facade\Config;
use think\facade\Request;


class Index extends Base{

    public function index(){
        // dev
        if (strpos(Request::domain(), 'localhost')){
            return view('index', ['token' => Jwt::getInstance()->createToken("user_id", "whngqdcmhdxxf")]);
        }

        $code = Request::get("code");
        if($code){
            $userBasic = WeChat::getInstance()->setCode($code)->getUserBasic();
            $userInfo  = WeChat::getInstance()->getUserInfo($userBasic["UserId"]);
            if($userBasic){
                $user = User::getInstance()->getUserByUserId($userBasic["UserId"]);
                if(!$user["data"]){
                    User::getInstance()->setUserInfo($userInfo)->createUser();
                }else{
                    User::getInstance()->setUserInfo($userInfo)->updateUser($userBasic["UserId"]);
                }
            }
        }else{
            $redirect = sprintf($this->authApi,$this->companyId,$this->redirect,$this->agentId);
            return redirect($redirect);
        }
       return view('index', [
           'token' => Jwt::getInstance()->createToken("user_id",$userBasic["UserId"])
       ]);
    }

    public function test(){
        $template = "<div class='normal'>会议名称:%s</div>";
        $template.= "<div class='normal'>主持人:%s</div>";
        $template.= "<div class='highlight'>参会时间:%s</div>";
        $title = "第三次代表大会";
        $host  = "张涛";
        $time  = "2018-5-20";
        $description =  sprintf($template,$title,$host,$time);
        $data = array (
            'toUser' => 'ZhangTao',
            'toParty' => '',
            'toTag' => '',
            'title' => '会议通知',
            'description' => $description,
            'url' => 'http://www.baidu.com',
            'btnTxt' => '更多',
        );
      $result =  WeChat::getInstance()->setPost($data,"textcard")->sendAgentMessage();
        var_dump($result);
    }
}

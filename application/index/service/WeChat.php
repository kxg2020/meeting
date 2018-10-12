<?php
namespace app\index\service;
use app\index\service\template\AgentMessage;
use think\facade\Cache;
use think\facade\Log;


class WeChat{
    use Singleton;
    // 应用ID
    const AGENT_ID     = 1000003;
    // 应用秘钥
    const AGENT_SECRET = "RMJaW3iZxl7T0plKk4qG_qaaAIkM4xeWz2S5HmfNzxk";
    // 企业ID
    const COMPANY_ID   = "ww0d4cee19e94134fd";
    // 企业接口
    const COMPANY_BASE_API  = "https://qyapi.weixin.qq.com/cgi-bin/";
    // 获取access_token
    const GET_ACCESS_TOKEN  = "gettoken?corpid=%s&corpsecret=%s";
    // 获取成员基础信息
    const GET_MEMBER_BASIC  = "user/getuserinfo?access_token=%s&code=%s";
    // 获取成员详细信息
    const GET_MEMBER_INFO   = "user/get?access_token=%s&userid=%s";
    // 获取部门列表信息
    const GET_DEPARTMENT    = "department/list?access_token=%s&id=%s";
    // 发送应用卡片消息
    const SEND_AGENT_MESSAGE= "message/send?access_token=%s";
    // 获取部门成员
    const GET_DEPARTMENT_MEMBER = "user/simplelist?access_token=%s&department_id=%s&fetch_child=%s";

    private $requestUrl;
    private $token = null;
    private $code  = null;
    private $post  = [];


    /*
    * 获取access_token
    */
    public function getCompanyAccessToken(){
        $this->token = \think\facade\Cache::get("access_token");
        if(!$this->token){
            $this->setUrl("accessToken");
            $result = Tool::getInstance()
                ->jsonDecode(Http::getInstance()->request($this->requestUrl));
            if(isset($result["errcode"]) && $result["errcode"] == 0){
                // 缓存token
                Cache::set("access_token",$result["access_token"],$result["expires_in"]);
                $this->token = $result["access_token"];
            }
        }
        return $this;
    }

    /*
     * 登录获取成员基础信息
     */
    public function getUserBasic(){
       return $this->getCompanyAccessToken()->setUrl("userBasic")->request();

    }

    /*
     * 获取用户详情
     */
    public function getUserInfo($userId){

       return $this->getCompanyAccessToken()->setUrl("userInfo",["user_id"=>$userId])->request();
    }

    /*
     * 获取部门列表
     */
    public function getDepartmentList(){
        return $this->getCompanyAccessToken()->setUrl("departmentList")->request();
    }

    /*
     * 发送应用消息
     */
    public function sendAgentMessage(){
       return $this->getCompanyAccessToken()->setUrl("sendAgentMessage")->request();
    }

    /*
     * 获取部门成员
     */
    public function getDepartmentMember($departmentId){
        return $this->getCompanyAccessToken()->setUrl("departmentMember",["id"=>$departmentId])->request();
    }

    /*
     * 发起请求
     */
    private function request(){
        $result = Tool::getInstance()->jsonDecode(Http::getInstance()->request($this->requestUrl,Tool::getInstance()->jsonEncode($this->post)));
        if(isset($result["errcode"]) && $result["errcode"] == 0){
            return $result;
        }
        Log::error(json_encode($result));
        return false;
    }

    private function setUrl($type,$param = []){
        switch($type){
            case "accessToken":
                $this->requestUrl = sprintf(self::COMPANY_BASE_API.self::GET_ACCESS_TOKEN,self::COMPANY_ID,self::AGENT_SECRET);
                break;
            case "userBasic":
                $this->requestUrl = sprintf(self::COMPANY_BASE_API.self::GET_MEMBER_BASIC,$this->token,$this->code);
                break;
            case "userInfo":
                $this->requestUrl = sprintf(self::COMPANY_BASE_API.self::GET_MEMBER_INFO,$this->token,$param["user_id"]);
                break;
            case "departmentList":
                $this->requestUrl = sprintf(self::COMPANY_BASE_API.self::GET_DEPARTMENT,$this->token,"");
                break;
            case "sendAgentMessage":
                $this->requestUrl = sprintf(self::COMPANY_BASE_API.self::SEND_AGENT_MESSAGE,$this->token);
                break;
            case "departmentMember":
                $this->requestUrl = sprintf(self::COMPANY_BASE_API.self::GET_DEPARTMENT_MEMBER,$this->token,$param["id"],"");
                break;
        }
        return $this;
    }

    public function setCode($code){
        $this->code = $code;
        return $this;
    }

    public function setPost(AgentMessage $agentMessage){
        $this->post = $agentMessage->trigger();
        return $this;
    }
}
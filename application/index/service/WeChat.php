<?php
namespace app\index\service;
use think\facade\Cache;


class WeChat{
    use Singleton;
    // 应用ID
    const AGENT_ID     = 1000003;
    // 应用秘钥
    const AGENT_SECRET = "RMJaW3iZxl7T0plKk4qG_qaaAIkM4xeWz2S5HmfNzxk";
    // 企业ID
    const COMPANY_ID   = "ww0d4cee19e94134fd";
    // 企业接口
    const COMPANY_BASE_API = "https://qyapi.weixin.qq.com/cgi-bin/";
    // 获取access_token
    const GET_ACCESS_TOKEN = "gettoken?corpid=%s&corpsecret=%s";
    // 获取成员信息
    const GET_MEMBER_INFO  = "getuserinfo?access_token=%s&code=%s";
    // 获取成员部门信息
    const GET_MEMBER_DEPARTMENT = "get?access_token=%s&userid=%s";

    private $requestUrl;
    private $token = null;
    private $code  = null;


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
        $this->getCompanyAccessToken()->setUrl("userInfo");
        $result = Tool::getInstance()
            ->jsonDecode(Http::getInstance()->request($this->requestUrl));
        if(isset($result["errcode"]) && $result["errcode"] == 0){
            return $result;
        }
        return false;
    }

    /*
     * 获取成员部门详情
     */
    public function getUserInfo($userId){
        $this->getCompanyAccessToken()
             ->setUrl("userDepartmentInfo",["user_id"=>$userId]);
        $result = Tool::getInstance()
            ->jsonDecode(Http::getInstance()->request($this->requestUrl));
        if(isset($result["errcode"]) && $result["errcode"] == 0){
            return $result;
        }
        return false;
    }

    private function setUrl($type,$param = []){
        $url = [
            "accessToken" => sprintf(self::COMPANY_BASE_API.self::GET_ACCESS_TOKEN,self::COMPANY_ID,self::AGENT_SECRET),
            "userInfo"     => sprintf(self::COMPANY_BASE_API.self::GET_MEMBER_INFO,$this->token,$this->code),
            "userDepartmentInfo" => sprintf(self::COMPANY_BASE_API.self::GET_MEMBER_DEPARTMENT,$this->token,$param["user_id"])
        ];
        $this->requestUrl = $url[$type];
        return $this;
    }

    public function setCode($code){
        $this->code = $code;
        return $this;
    }
}
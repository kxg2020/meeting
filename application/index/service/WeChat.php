<?php
namespace app\index\service;
use think\facade\Cache;


class WeChat{
    use Singleton;
    // Ӧ��ID
    const AGENT_ID     = 1000003;
    // Ӧ����Կ
    const AGENT_SECRET = "RMJaW3iZxl7T0plKk4qG_qaaAIkM4xeWz2S5HmfNzxk";
    // ��ҵID
    const COMPANY_ID   = "ww0d4cee19e94134fd";
    // ��ҵ�ӿ�
    const COMPANY_BASE_API = "https://qyapi.weixin.qq.com/cgi-bin/";
    // ��ȡaccess_token
    const GET_ACCESS_TOKEN = "gettoken?corpid=%s&corpsecret=%s";
    // ��ȡ��Ա��Ϣ
    const GET_MEMBER_INFO  = "getuserinfo?access_token=%s&code=%s";
    // ��ȡ��Ա������Ϣ
    const GET_MEMBER_DEPARTMENT = "get?access_token=%s&userid=%s";

    private $requestUrl;
    private $token = null;
    private $code  = null;


    /*
    * ��ȡaccess_token
    */
    public function getCompanyAccessToken(){
        $this->token = \think\facade\Cache::get("access_token");
        if(!$this->token){
            $this->setUrl("accessToken");
            $result = Tool::getInstance()
                ->jsonDecode(Http::getInstance()->request($this->requestUrl));
            if(isset($result["errcode"]) && $result["errcode"] == 0){
                // ����token
                Cache::set("access_token",$result["access_token"],$result["expires_in"]);
                $this->token = $result["access_token"];
            }
        }
        return $this;
    }

    /*
     * ��¼��ȡ��Ա������Ϣ
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
     * ��ȡ��Ա��������
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
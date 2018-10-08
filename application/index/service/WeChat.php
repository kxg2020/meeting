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
    const COMPANY_BASE_API  = "https://qyapi.weixin.qq.com/cgi-bin/";
    // ��ȡaccess_token
    const GET_ACCESS_TOKEN  = "gettoken?corpid=%s&corpsecret=%s";
    // ��ȡ��Ա������Ϣ
    const GET_MEMBER_BASIC  = "user/getuserinfo?access_token=%s&code=%s";
    // ��ȡ��Ա��ϸ��Ϣ
    const GET_MEMBER_INFO   = "user/get?access_token=%s&userid=%s";
    // ��ȡ�����б���Ϣ
    const GET_DEPARTMENT    = "department/list?access_token=%s&id=%s";
    // ����Ӧ����Ϣ
    const SEND_AGENT_MESSAGE= "message/send?access_token=%s";

    private $requestUrl;
    private $token = null;
    private $code  = null;
    private $post  = [];


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
       return $this->getCompanyAccessToken()->setUrl("userBasic")->request();

    }

    /*
     * ��ȡ��Ա��������
     */
    public function getUserInfo($userId){

       return $this->getCompanyAccessToken()->setUrl("userInfo",["user_id"=>$userId])->request();
    }

    /*
     * ��ȡ�����б�
     */
    public function getDepartmentList(){
        return $this->getCompanyAccessToken()->setUrl("departmentList")->request();
    }

    /*
     * ����Ӧ����Ϣ
     */
    public function sendAgentMessage(){
       return $this->getCompanyAccessToken()->setUrl("sendAgentMessage")->request();
    }

    /*
     * ��������
     */
    private function request(){
        $result = Tool::getInstance()->jsonDecode(Http::getInstance()->request($this->requestUrl,Tool::getInstance()->jsonEncode($this->post)));
        if(isset($result["errcode"]) && $result["errcode"] == 0){
            return $result;
        }
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
        }
        return $this;
    }

    public function setCode($code){
        $this->code = $code;
        return $this;
    }

    public function setPost($params,$type){
        $this->post = [
            "touser"   => $params["toUser"],
            "toparty"  => $params["toParty"],
            "totag"    => $params["toTag"],
            "msgtype"  => $type,
            "agentid"  => self::AGENT_ID,
            "textcard" => [
                "title"       => $params["title"],
                "description" => $params["description"],
                "url"         => $params["url"],
                "btntxt"      => $params["btnTxt"],
            ],
        ];
        return $this;
    }
}
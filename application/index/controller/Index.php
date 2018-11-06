<?php
namespace app\index\controller;
use app\index\model\Department;
use app\index\model\User;
use app\index\service\Jwt;
use app\index\service\Tool;
use app\index\service\WeChat;
use Mpdf\Mpdf;
use think\facade\Request;



class Index extends Base{

    public function index(){
        // dev
        if (strpos(Request::domain(), 'localhost')){
            return $this->indexView(
                Jwt::getInstance()->createToken("user_id", "whngqdcmhdxxf"),
                [
                    "name" => 'name',
                    "position" => 'position',
                    "avatar" => 'https://img.it9g.com/other/FvO_Csuv2DyvYZxzc97xjxLWyoeO.jpeg'
                ],
                [87, 88],
                '',
                ''
            );
        }
        $code = Request::get("code");
        $ifRedirect = Request::get("redirect") ? Request::get("redirect") : "";
        $this->redirect = urlencode($this->redirect.'?redirect='.$ifRedirect);
        if($code){
            $userBasic = WeChat::getInstance()->setCode($code)->getUserBasic();
            $userInfo  = User::getInstance()->getUserByUserId($userBasic["UserId"]);
            if($userBasic){
                $userInfo["data"]["department"] = Tool::getInstance()->jsonDecode($userInfo["data"]["department"]);
                $viewPermission = Department::getInstance()
                    ->loginUserViewPermission($userInfo["data"]["department"],$userInfo);
                $viewPermissionId = [];
                if($viewPermission["data"]){
                    array_walk($viewPermission["data"],function ($value) use (&$viewPermissionId){
                        $viewPermissionId[] = $value["meetingTypeId"];
                    });
                }
                return $this->indexView(
                    Jwt::getInstance()->createToken("user_id",$userBasic["UserId"]),
                    [
                        "name"     => $userInfo["data"]['name'],
                        "position" => $userInfo["data"]["position"],
                        "avatar"   => $userInfo["data"]['avatar']
                    ],
                    $viewPermissionId,
                    $ifRedirect
                );
            }else{
                $redirect = sprintf($this->authApi,$this->companyId,$this->redirect,$this->agentId);
                return \redirect($redirect);
            }
        }else{
            $redirect = sprintf($this->authApi,$this->companyId,$this->redirect,$this->agentId);
            return \redirect($redirect);
        }

    }

    public function pdf(){
        $pdf = new Mpdf([
            'default_font' => 'GB',
            'format' => 'A4',
        ]);
        $pdf->use_kwt = true;
        $pdf->autoScriptToLang = true;
        $pdf->setFooter('{PAGENO}');
        $html = file_get_contents('../application/index/view/index/pdf.php');
        $pdf->WriteHTML($html);
        $pdf->Output(date("Y-m-d H:i:s").".pdf","I");
        exit;
//        return view('pdf');
    }

}

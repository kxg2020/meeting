<?php
namespace app\index\controller;

use app\index\model\MeetingType;
use app\index\model\UserMeeting;
use app\index\service\Tool;
use think\facade\Request;
use think\facade\Url;

class MeetingRecord extends Base {

    /*
     * �����б�������
     */
    public function meetingCreate(){
        $params = Tool::getInstance()->jsonDecode(file_get_contents("php://input"));
        $result = \app\index\model\MeetingRecord::getInstance()
            ->createMeetingRecord($params);
        return $this->printResponse($result["code"]);
    }

    /*
     * ɾ������
     */
    public function meetingDelete(){
        $meetingId = Request::param("meetingId");
        $userRole = \app\index\model\User::getInstance()->getUserByUserId(Request::instance()->userId);
        if(!empty($userRole["data"]["department"])){
            $department = Tool::getInstance()->jsonDecode($userRole["data"]["department"]);
            if(in_array(1,$department)){
                $result = \app\index\model\MeetingRecord::getInstance()->meetingDelete($meetingId);
                if($result){
                    return $this->printResponse(9004);
                }
                return $this->printResponse(4007);
            }
            return $this->printResponse(4004);
        }
        return $this->printResponse(4004);
    }

    /*
     *ĳ�ֻ���Ļ����б�
     */
    public function meetingRecordList(){
        $typeId = Request::param("typeId");
        $params = $this->formatPage(Request::param());
        $records = \app\index\model\MeetingRecord::getInstance()->getMeetingRecords($typeId,$params);
        if($records["status"]){
            return $this->printResponse(200, $records["data"]);
        }
        return $this->printResponse();
    }

    /*
     * ��������������б�
     */
    public function singleMeetingInfo(){
        $userId    = request()->userId;
        $meetingId = Request::param("meetingId");
        $result = UserMeeting::getInstance()->userMeetingRecord($meetingId);
        if(!$result["data"]){
            UserMeeting::getInstance()->createUserMeetingMap($userId,$meetingId);
        }
       $result = \app\index\model\MeetingRecord::getInstance()
            ->singleMeetingInfo($meetingId);
        if($result["data"]){
            return $this->printResponse(200,$result["data"]);
        }
        return $this->printResponse();
    }

    /*
     * �����¼
     */
    public function meetingRecordWord(){
        $meetingId = Request::get("meetingId");
        return view("meeting/word");
    }
}
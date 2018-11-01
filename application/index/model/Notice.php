<?php

namespace app\index\model;

use app\index\service\Singleton;
use app\index\service\Tool;
use think\Db;

class Notice extends Base
{
    use Singleton;

    public function createNotice(array $params)
    {
        $insert = [
            "title" => $params["title"],
            "content" => $params["content"],
            "images" => Tool::getInstance()->jsonEncode(isset($params["images"]) ? $params["images"] : []),
            "create_time" => time()
        ];
        return Db::name("notice")->insertGetId($insert);
    }

    public function deleteNotice($noticeId)
    {
        $result = Db::name("notice")->where(["id" => $noticeId])->delete();
        if ($result) {
            return true;
        }
        return false;
    }

    public function detailNotice($noticeId): ?array
    {
        $result = Db::name("notice")->where(["id" => $noticeId])->find();
        if ($result) {
            $result["images"] = Tool::getInstance()->jsonDecode($result['images']);
            $result["create_time"] = date("Y-m-d H:i:s", $result["create_time"]);
            return $result;
        }
        return null;
    }

    public function getNoticeList($page, $page_size)
    {
        $notice_list = Db::name("notice")
            ->limit($this->formatLimit($page, $page_size), $page_size)
            ->order("id desc")
            ->select();
        $total = Db::name("notice")->count();
        if ($notice_list) {
            array_walk($notice_list, function (&$value) use (&$result) {
                $value["create_time"] = date("Y-m-d H:i:s", $value["create_time"]);
            });
        } else {
            $notice_list = [];
            $total = 0;
        }
        return [
            "notice_list" => $notice_list,
            "total" => $total
        ];
    }
}
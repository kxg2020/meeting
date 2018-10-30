<?php
namespace app\index\service;

class Enum{
    const MeetingTypeShortName = [
        "yz" => "read",
        "bj" => "ballot",
        "tp" => "votes"
    ];
    const READ   = "yz";
    const BALLOT = "bj";
    const VOTE   = "tp";
    const ADMIN  = "超级管理员";
}
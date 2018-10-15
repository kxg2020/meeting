<?php
namespace app\index\service\template;

class AgentMessageFacade{
    public static $instance = null;


    public static function __callStatic($name, $arguments){
        return self::make($name);
    }

    public static function make($class){
        $class = "app\\index\\service\\template\\$class";
        self::$instance || self::$instance = new $class();
        return self::$instance;
    }

}
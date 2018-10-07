<?php
namespace app\index\service;

trait Singleton{
    public static $instance = null;

    /**
     * @return static
     */
    public static function getInstance(){
        self::$instance || self::$instance = new self();
        return self::$instance;
    }

    private function __clone(){

    }


}
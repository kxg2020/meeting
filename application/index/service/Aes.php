<?php
namespace app\index\service;

class Aes{
    private $method;
    private $key;
    private $iv;
    private $options;
    use Singleton;

    public function __construct($key, $method = 'AES-128-ECB', $iv = '', $options = 0){
        $this->key        = isset($key) ? $key : 'meeting';
        $this->method     = $method;
        $this->iv         = $iv;
        $this->options    = $options;
    }

    public function encrypt($data){
        return openssl_encrypt($data, $this->method, $this->key, $this->options, $this->iv);
    }

    public function decrypt($data){
        return openssl_decrypt($data, $this->method, $this->key, $this->options, $this->iv);
    }
}
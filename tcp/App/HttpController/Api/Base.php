<?php
namespace App\HttpController\Api;

class Base extends \App\HttpController\Common {
    protected $userId = 0;
    protected $user = null;
    protected $type = 0;
    protected function onRequest(?string $action) : ?bool {
        parent::onRequest($action);
        
        try {
            if(empty($this->POST)) return $this->errorResult();
            
            $nonce = $this->POST['nonce'] ?? '';
            if(empty($nonce) || !is_string($nonce)) return $this->errorResult();
            unset($this->POST['nonce']);
            $nonce = substr($nonce, 0, 27);
            
            $time = $this->POST['key'] ?? '';
            if(empty($time) || !is_numeric($time)) return $this->errorResult();
            unset($this->POST['key']);
            
            $sign = $this->POST['sign'] ?? '';
            if(empty($sign) || !is_string($sign)) return $this->errorResult();
            unset($this->POST['sign']);
            $len = strlen($sign);
            
            ksort($this->POST);
            
            $this->POST['nonce'] = substr($sign, $len - 27); //sign最后的27字符才是真正的nonce
            $str = http_build_query($this->POST) . '&timestamp=' . $time;
            
            $sign = substr($sign, 0, $len - 27) . $nonce;
            
            return strtolower(hash_hmac('sha256', $str, $time)) === $sign;
        } catch (\Exception $e) {
            
        }
        
        return false;

    }
}
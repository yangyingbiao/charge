<?php
namespace App\HttpController\Api;

class Base extends \App\HttpController\Common {
    protected $userId = 0;
    protected $user = null;
    const whiteList = ['login/login', 'sys/config'];
    protected function onRequest(?string $action) : ?bool {
        parent::onRequest($action);
        
        $path = $this->request()->getUri()->getPath();
        if(strpos($path, '/api') !== 0) {
            return $this->errorResult();
        }
        
        $path = substr($path, 7);
        
        return true;
        
    }
}
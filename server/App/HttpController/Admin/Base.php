<?php
namespace App\HttpController\Admin;

class Base extends \App\HttpController\Common {
    protected $userId = 0;
    protected $user = null;
    protected $type = 0;
    const whiteList = ['login/login', 'sys/config']; 
    protected function onRequest(?string $action) : ?bool {
        parent::onRequest($action);
        
        $path = $this->request()->getUri()->getPath();
        if(strpos($path, '/admin') !== 0) {
            return $this->errorResult();
        }
        
        $path = substr($path, 7);
        
        try {
            if(!in_array($path, self::whiteList)) {
                $request = $this->request();
                $token = $request->getHeader('authorization');
                if(empty($token[0])) {
                    return $this->errorResult('请登录1', 404);
                }
                $jwtSecret = getConfig('ADMIN.jwt_secret.login');
                $jwtObject = \EasySwoole\Jwt\Jwt::getInstance()->setSecretKey(getConfig('ADMIN.jwt_secret.login'))->decode($token[0]);
                if($jwtObject->getStatus() !== 1) {
                    return $this->errorResult('请登录2', 404);
                }
                $data = $jwtObject->getData();
                $randLen = strlen(strval($data['key'] / 22));
                $userId = intval(substr($jwtObject->getJti(), $randLen)) / 69;
                $user = getRedis()->get('user.' . $userId);
                if(empty($user)) $this->errorResult('请登录3', 404);
                $user = json_decode($user, true);
                
                if($user['super'] != 1) { //非超级管理员
                    //判断权限
                    $paths = explode('/', $path);
                    $route = ROUNTE[$paths[0]][$paths[1]];
                    if(!empty($route[2])) { //需要访问权限的
                        if(empty($user['permission']) || !in_array($route[2], $user['permission'])) { //需要权限
                            return $this->errorResult('无访问权限');
                        }
                        
                        unset($user['permission']);
                    }
                }
                
                $this->user = $user;
                $this->userId = $userId;
            }
            
            return true;
        } catch (\Exception $e) {
            
        }
        
        
        return false;

    }
}
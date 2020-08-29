<?php


namespace App\HttpController;

use EasySwoole\Jwt\Jwt;

class Index extends Base
{

    public function index()
    {   
        $time = time();
        $admin = ['user_id' => 100];
        
        $key  = md5(ceil(($time / 1000)) . mt_rand(1000, 699999) . uniqid() . $admin['user_id']);
        
        $jwtSecret = getConfig('ADMIN.jwt_secret');
        
        $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJleHAiOjE1OTE2MTIzODAsInN1YiI6bnVsbCwibmJmIjoxNTkxNjA1MTgwLCJhdWQiOiLlsI_mnagiLCJpYXQiOjE1OTE2MDUxODAsImp0aSI6IjgxNjkxMjEwMCIsInN0YXR1cyI6MSwiZGF0YSI6eyIwIjoi5oiR54ix54i454i45aaI5aaIIiwia2V5IjozMTg1OTQ5fX0.P1HJjYLVhSY9Usnx7y3OI1vcXyNLTf_OHW8lSGts7So';
        $jwtObject = Jwt::getInstance()->setSecretKey($jwtSecret['refresh'])->decode($token);
        //var_dump($jwtObject->getStatus());
        
        $data = $jwtObject->getData();
        $randLen = strlen(strval($data['key'] / 39));
        $userId = intval(substr($jwtObject->getJti(), $randLen)) / 21;
        
        var_dump($userId);
        
        return $this->errorResult();
        
            
        $this->successResult(['nick_name' => $refreshToken]);
    }

    protected function actionNotFound(?string $action)
    {
        $this->response()->withStatus(404);
        $file = EASYSWOOLE_ROOT.'/vendor/easyswoole/easyswoole/src/Resource/Http/404.html';
        if(!is_file($file)){
            $file = EASYSWOOLE_ROOT.'/src/Resource/Http/404.html';
        }
        $this->response()->write(file_get_contents($file));
    }
}
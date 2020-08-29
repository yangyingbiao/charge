<?php
namespace App\HttpController\Admin;
use EasySwoole\Jwt\Jwt;

class Login extends Base {
    private static function buildToken($userId, $secret, $time) {
        $tokenRand = mt_rand(100000, 999999);
        return Jwt::getInstance()->setSecretKey($secret)->publish()
        ->setAlg('HMACSHA256')
        ->setAud('小杨')
        ->setExp($time + 7200)
        ->setIat($time)
        ->setIss('bill')
        ->setJti($tokenRand . ($userId * 69))
        ->setData([
            '我爱爸爸妈妈',
            'key' => $tokenRand * 22
        ])
        ->__toString();
    }
    
    private static function buildRefreshToken($userId, $secret, $time) {
        $refreshRand = mt_rand(10000, 99999);
        return Jwt::getInstance()->setSecretKey($secret)->publish()
        ->setAlg('HMACSHA256')
        ->setAud('小杨')
        ->setExp($time + 7200)
        ->setIat($time)
        ->setIss('bill')
        ->setJti($refreshRand . ($userId * 21))
        ->setData([
            '我爱爸爸妈妈',
            'key' => $refreshRand * 39
        ])
        ->__toString();
    }
    
    public function refresh() {
        $token = $this->POST['token'] ?? '';
        if(empty($token)) {
            return $this->errorResult();
        }
        
        $jwtSecret = getConfig('ADMIN.jwt_secret');
        
        $jwtObject = Jwt::getInstance()->setSecretKey($jwtSecret['refresh'])->decode($token);
        if($jwtObject->getStatus() !== 1) {
            return $this->errorResult();
        }
        
        $data = $jwtObject->getData();
        $randLen = strlen(strval($data['key'] / 39));
        $userId = intval(substr($jwtObject->getJti(), $randLen)) / 21;
        $user = getRedis()->get('user.' . $userId);
        if(empty($user)) return $this->errorResult();
        
        $time = time();
        
        $token = self::buildToken($userId, $jwtSecret['login'], $time);
        $refreshToken = self::buildRefreshToken($userId, $jwtSecret['refresh'], $time);
        
        return $this->successResult(['token' => $token, 'refreshToken' => $refreshToken, 'key' => uniqid()]);
    }
    
    public function login() {
        
//         $client = curl_init('http://127.0.0.1:10000/Api/Test/send');
//         curl_setopt($client, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
//         curl_setopt($client, CURLOPT_POST, 1); //设置为POST方式
//         curl_setopt($client, CURLOPT_RETURNTRANSFER, true);
//         curl_setopt($client, CURLOPT_POSTFIELDS, json_encode($this->POST));//POST数据
//         $body = curl_exec($client);
//         curl_close($client);
//         var_dump($body);
        
//         return $this->errorResult();
        
        $params = $this->POST;
        $account = isset($params['account']) ? trim($params['account']) : '';
        if($account === '') {
            return $this->errorResult();
            
        }

        $password = isset($params['password']) ? trim($params['password']) : '';
        if($password === '') {
            return $this->errorResult();
        }
        
        $loginFailCountKey = $account . '.login.fail';
        
        $redis = getRedis();
        
        $loginFailCount = $redis->get($loginFailCountKey) ?? 0;
        if($loginFailCount >= 5) {
            return $this->errorResult('您今天不能再登录，请于明天尝试登录'); 
        }

        $time = time();
        
        $adminModel = model('admin');
        
        $admin = $adminModel->findOne(['account' => $account], ['user_id', 'type', 'super', 'salt', 'password', 'role', 'status']);
        if($admin['status'] == 0) return $this->errorResult('您的账号已被禁用，请联系管理员');
        
        if(empty($admin) || (md5(md5($password) . $admin['salt']) !== $admin['password'])) {
            
            getTask()->async(function() use($loginFailCount, $loginFailCountKey, $time) {
                if($loginFailCount == 0) {
                    $nextDay = 86400 + strtotime(date('Y-m-d 00:00:00'), $time);
                    getRedis()->set($loginFailCountKey, 1, strtotime(date('Y-m-d 00:00:00'), $time)  + 86400 - $time - 1); //当天23:59:59过期
                }else {
                    getRedis()->incr($loginFailCountKey);
                }
            });
            
            return $this->errorResult('账号或密码错误，今天您还剩余' . (4 - $loginFailCount) . '次登录机会');
        }
        
        if($admin['super'] != 1 && $admin['role']) { //非超级管理员
            $admin['role'] = json_decode($admin['role'], true);
            //查询权限
            $permissions = model('permissionRole')->findAll($admin['role'], ['permission', 'status']);
            if(!empty($permissions)) {
                $permissionList = [];
                foreach ($permissions as $item) {
                    if($item['status'] != 1 || empty($item['permission'])) continue;
                    $permissionList = array_merge($permissionList, json_decode($item['permission'], true));
                }
                
                if(!empty($permissionList)) {
                    $admin['permission'] = $permissionList;
                }
            }
        }
        
        
        
        $jwtSecret = getConfig('ADMIN.jwt_secret');
        
        $token = self::buildToken($admin['user_id'], $jwtSecret['login'], $time);
        $refreshToken = self::buildRefreshToken($admin['user_id'], $jwtSecret['refresh'], $time);
        
        unset($admin['password']);
        unset($admin['salt']);
        unset($admin['role']);
        
        getTask()->async(function() use( $admin, $account) {
            $admin['account'] = $account;
            getRedis()->set('user.' . $admin['user_id'], json_encode($admin));
        });
        
        return $this->successResult(['token' => $token, 'refreshToken' => $refreshToken, 'key' => uniqid(), 'user' => $admin]);
    }
}
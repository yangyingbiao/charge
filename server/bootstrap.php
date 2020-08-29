<?php

define('ROUNTE', require __DIR__ . '/config/router.php');

function model($name) {
    $className = '\\App\Model\\' . ucfirst($name);
    return new $className();
}

//将下划线命名转换为驼峰式命名
function strToHump( $str) {
    if(is_numeric($str)) return $str;
	$end = strlen($str) - 1;
    while(true)
	{
		$pos = strpos($str , '_', 1);
		if($pos === false || $pos == (strlen($str) - 1)) break;
		$str = substr($str , 0 , $pos).ucfirst(substr($str , $pos+1));
	}
 
    return $str;
}

function getRedis() {
    $redis = \EasySwoole\RedisPool\Redis::defer('redis');
    if(empty($redis)) {
        throw new Exception('get redis fail');
    }else {
        return $redis;
    }
}

function getTask() {
    return \EasySwoole\EasySwoole\Task\TaskManager::getInstance();
}

function getConfig($keyPath = '') {
    return \EasySwoole\EasySwoole\Config::getInstance()->getConf($keyPath);
}

function buildOrderNo($userId = 0){
    if($userId){
        $userId = str_pad($userId,5, '02', STR_PAD_LEFT);
    }
    
    $sn = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    
    if($userId){
        $sn .= $userId;
    }
    
    return $sn;
}
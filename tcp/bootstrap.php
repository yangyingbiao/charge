<?php

function model($name) {
    $className = '\\App\Model\\' . ucfirst($name);
    return new $className();
}

//将下划线命名转换为驼峰式命名
function strToHump ( $str)
{
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
    $redisPool = \EasySwoole\RedisPool\Redis::getInstance()->get('redis');
    if(empty($redisPool)) {
        throw new Exception('get redis fail');
    }else {
        return $redisPool->getObj();
    }
}

function getTask() {
    return \EasySwoole\EasySwoole\Task\TaskManager::getInstance();
}

function getConfig($keyPath = '') {
    return \EasySwoole\EasySwoole\Config::getInstance()->getConf($keyPath);
}
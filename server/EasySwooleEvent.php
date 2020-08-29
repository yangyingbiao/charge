<?php
namespace EasySwoole\EasySwoole;


use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;

use EasySwoole\ORM\Db\Connection;
use EasySwoole\ORM\DbManager;

class EasySwooleEvent implements Event
{

    public static function initialize()
    {
        date_default_timezone_set('Asia/Shanghai');

        $configInstance = Config::getInstance();

        //mysql数据库
        DbManager::getInstance()->addConnection(new Connection(new \EasySwoole\ORM\Db\Config($configInstance->getConf('MYSQL'))));
        
        //redis
        $redisPoolConfig = \EasySwoole\RedisPool\Redis::getInstance()->register('redis', new \EasySwoole\Redis\Config\RedisConfig($configInstance->getConf('REDIS')));
        $redisPoolConfig->setMinObjectNum(5);
        $redisPoolConfig->setMaxObjectNum(20);
        $redisPoolConfig->setIntervalCheckTime(10);
    }

    public static function mainServerCreate(EventRegister $register)
    {
        
        
        
    }

    public static function onRequest(Request $request, Response $response): bool
    {
        // TODO: Implement onRequest() method.
        return true;
    }

    public static function afterRequest(Request $request, Response $response): void
    {
        // TODO: Implement afterAction() method.
    }
}
<?php
namespace EasySwoole\EasySwoole;


use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;

class EasySwooleEvent implements Event
{

    public static function initialize()
    {
        // TODO: Implement initialize() method.
        date_default_timezone_set('Asia/Shanghai');
    }

    public static function mainServerCreate(EventRegister $register)
    {
        
        $ser = ServerManager::getInstance()->getSwooleServer();
        $subPort = $ser->addlistener('0.0.0.0', 7777, SWOOLE_TCP);
        
        $subPort->on('connect', function (\swoole_server $server, int $fd, int $reactor_id) {
            echo "fd:{$fd} 已连接\n";
            $str = '恭喜你连接成功';
            $server->send($fd, $str);
        });
        
        $subPort->on('close', function (\swoole_server $server, int $fd, int $reactor_id) {
            echo "fd:{$fd} 已关闭\n";
        });
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
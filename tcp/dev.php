<?php
return [
    'SERVER_NAME' => "EasySwoole",
    'MAIN_SERVER' => [
        'LISTEN_ADDRESS' => '127.0.01',
        'PORT' => 10000,
        'SERVER_TYPE' => EASYSWOOLE_WEB_SERVER, //可选为 EASYSWOOLE_SERVER  EASYSWOOLE_WEB_SERVER EASYSWOOLE_WEB_SOCKET_SERVER,EASYSWOOLE_REDIS_SERVER
        'SOCK_TYPE' => SWOOLE_TCP,
        'RUN_MODEL' => SWOOLE_PROCESS,
        'SETTING' => [
            'worker_num' => 8,
            'reload_async' => true,
            'max_wait_time'=>3
        ],
        'TASK'=>[
            'workerNum'=>4,
            'maxRunningNum'=>128,
            'timeout'=>15
        ]
    ],
    'TEMP_DIR' => null,
    'LOG_DIR' => null,
    
    'API_KEY' => '32423FERDF3FE4TDG',
    
    'MYSQL' => [
        'database' => 'charge',
        'host' => '127.0.0.1',
        'user' => 'root',
        'password' => 'root',
        'port' => 3306,
        'charset' => 'utf8mb4'
    ],
    
    'REDIS' => [
        'host' => '127.0.0.1',
        'port' => 10301,
        'auth' => 'bill123'
    ]
];

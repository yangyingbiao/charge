<?php
return [
    'SERVER_NAME' => "EasySwoole",
    'MAIN_SERVER' => [
        'LISTEN_ADDRESS' => '0.0.0.0',
        'PORT' => 9501,
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

    
    'ADMIN' => [
        'jwt_secret' => [
            'login' => 'fvwstyfhreyhazqopnbf@^&#*GEWY4E63425475587@^&(*($&#$*&47467DHGEGVSDYTXDGHFTR$#^&#%hDGHD1561812385234&^#$^#^#^#%^$^#AAAAAAAAADDDDDDDDDDFFFFFFFF',
            'refresh' => '!#@$#(JTHFTYHFRTH35gdgthdfg&*$^&$#^&*#$HYgtfthyg545247%#%GDSGsfgdsdgg5174521615132fdsgcxggbv!@##$$%^&&^&&&&&&&cvfgbfgbfgvhgfgfgfffffff'
        ]
    ],
    
    'SYS' => [
        'price_pay_type' => [1 => '线上', 2 => '线下'],
        'price_charge_type' => [1 => '计时', 2 => '计量']
    ],

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
        'auth' => 'bill123',
        'timeout' => 10
    ]
];

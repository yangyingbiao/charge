<?php
return [
    'login' => [
        'login' => ['POST', '', ''],
        'refresh' => ['POST', 'refresh', '']
    ],
    
    'area' => [
        'areaList' => ['GET', '', '']
    ],
    
    'sys' => [
        'config' => ['GET', 'conf', '']
    ],
    
    'device' => [
        'deviceList' => ['GET', 'list', 'deviceList'],
        'addDevice' => ['POST', 'add', 'addDevice'],
        'editDevice' => ['PUT', 'edit', 'editDevice'],
        'editInfo' => ['GET', 'editInfo', 'editDevice'],
        'deleteDevice' => ['DELETE', 'delete', 'deleteDevice'],
    ],
    
    'chargePrice' => [
        'priceDetail' => ['GET', '', ''],
        'priceList' => ['GET', 'list', 'chargePriceList'],
        'getPriceList' => ['GET', 'getList', 'chargePriceList'],
        'addPrice' => ['POST', 'add', 'addChargePrice'],
        'editPrice' => ['PUT', 'edit', 'editChargePrice'],
        'deletePrice' => ['DELETE', 'delete', 'deleteChargePrice'],
    ],
    
    'deviceType' => [
        'getDeviceTypeList' => ['GET', 'getType', 'deviceTypeList'],
        'typeList' => ['GET', 'list', 'deviceTypeList'],
        'addType' => ['POST', 'add', 'addDeviceType'],
        'editType' => ['PUT', 'edit', 'editDeviceType'],
        'deleteType' => ['DELETE', 'delete', 'deleteDeviceType']
    ],
    
    'networkType' => [
        'getTypeList' => ['GET', 'getType', 'networkTypeList'],
        'typeList' => ['GET', 'list', 'networkTypeList'],
        'addType' => ['POST', 'add', 'addNetworkType'],
        'editType' => ['PUT', 'edit', 'editNetworkType'],
        'deleteType' => ['DELETE', 'delete', 'deleteNetworkType']
    ],
    
    'permission' => [
        'roleList' => ['GET', 'list', 'permissionRole'],
        'addRole' => ['POST', 'add', 'addPermissionRole'],
        'roleEditInfo' => ['GET', 'role', 'editPermissionRole'],
        'editRole' => ['POST', 'edit', 'editPermissionRole'],
        'changeStatus' => ['PUT', 'changeStatus', 'editPermissionRole'],
        
        'deleteRole' => ['DELETE', 'delete', 'deletePermissionRole'],
        
        'roleOption' => ['GET', 'option', 'permissionRole']
    ],
    
    'account' => [
        'accountList' => ['GET', 'list', 'accountList'],
        'addAccount' => ['POST', 'add', 'addAccount'],
        
        'account' => ['GET', '', 'editAccount'],
        'editAccount' => ['POST', 'edit', 'editAccount'],
        'changeStatus' => ['PUT', 'changeStatus', 'editAccount'],
        'deleteAccount' => ['DELETE', 'delete', 'deleteAccount'],
        'resetPassword' => ['PUT', 'resetPassword', 'resetAccountPassword'],
    ]
];
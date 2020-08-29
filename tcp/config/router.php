<?php
return [
    'login' => [
        'login' => ['POST', '', ''],
        'refresh' => ['POST', 'refresh', '']
    ],
    
    'deviceType' => [
        'typeList' => ['GET', 'list', 'deviceTypeList'],
        'addType' => ['POST', 'add', 'addDeviceType'],
        'editType' => ['PUT', 'edit', 'editDeviceType'],
        'deleteType' => ['DELETE', 'delete', 'deleteDeviceType']
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
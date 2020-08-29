create database charge charset=utf8mb4 COLLATE = utf8mb4_unicode_ci;

# 管理员
drop table if exists `admin`;
create table `admin`(
`user_id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
`account` VARCHAR(30) NOT NULL DEFAULT '' COMMENT '登录账号',
`password` CHAR(32) NOT NULL DEFAULT '' COMMENT '密码',
`salt` CHAR(6) NOT NULL DEFAULT '' COMMENT '密码盐',
`name` VARCHAR(10) NOT NULL DEFAULT '' COMMENT '',
`type` TINYINT NOT NULL DEFAULT 0 COMMENT '用户类型',
`creater` SMALLINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建者',
`role` JSON,
`super` TINYINT NOT NULL DEFAULT 0 COMMENT '是不是超级管理员',
`status` TINYINT NOT NULL DEFAULT 0 COMMENT '0:禁用，1：启用',
`remark` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '备注',
`create_time` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间戳',
PRIMARY KEY(`user_id`),
UNIQUE KEY(`account`)
) ENGINE=INNODB COMMENT='管理员表' AUTO_INCREMENT=1030;

# 原始密码 123456，盐 37c527
insert into `admin` (`account`, `name`, `password`, `salt`, `super`, `status`, `create_time`) values ('admin', '超级管理员', 'be9d6f3f9cfb2dc67f8ee19610075e14', '37c527', 1, 1, unix_timestamp());


# 设备类型
drop table if exists `device_type`;
create table `device_type`(
`id` TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
`name` VARCHAR(10) NOT NULL DEFAULT '' COMMENT '',
`level` TINYINT NOT NULL DEFAULT 0 COMMENT '层级',
`parent_id` TINYINT NOT NULL DEFAULT 0 COMMENT '',
PRIMARY KEY(`id`)
) ENGINE=MYISAM COMMENT='设备类型表' AUTO_INCREMENT=1;

# 设备表
drop table if exists `device`;
create table `device`(
`device_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
`device_no` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '设备编号',
`sim_iccid` VARCHAR(20) NOT NULL DEFAULT '' COMMENT 'SIM ICCID',
`create_time` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
`update_time` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
PRIMARY KEY(`device_id`),
UNIQUE KEY(`device_no`,`sim_iccid`)
) ENGINE=INNODB COMMENT='设备表' AUTO_INCREMENT=901030;

# 权限角色表
drop table if exists `permission_role`;
create table `permission_role`(
`id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
`name` VARCHAR(10) NOT NULL DEFAULT '' COMMENT '',
`user_id` SMALLINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '所属用户',
`permission` JSON COMMENT '权限',
`status` TINYINT NOT NULL DEFAULT 0 COMMENT '0:禁用，1：启用',
`remark` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '备注',
`create_time` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
PRIMARY KEY(`id`)
) ENGINE=MYISAM COMMENT='权限角色表' AUTO_INCREMENT=1030;





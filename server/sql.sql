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

# 设备通讯方式
drop table if exists `network_type`;
create table `network_type`(
`id` TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
`name` VARCHAR(10) NOT NULL DEFAULT '' COMMENT '',
PRIMARY KEY(`id`)
) ENGINE=MYISAM COMMENT='设备通讯方式' AUTO_INCREMENT=1;


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


# 设备表
drop table if exists `device`;
create table `device`(
`device_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
`device_name` VARCHAR(10) NOT NULL DEFAULT '' COMMENT '设备名称',
`sim_iccid` VARCHAR(20) NOT NULL DEFAULT '' COMMENT 'SIM ICCID',
`port_count` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '端口数量',
`device_type` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '设备类型',
`network_type` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '通讯类型',
`price_t` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '计时充电套餐',
`price_w` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '计量充电套餐',
`province_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '省',
`city_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '市',
`district_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '区',
`province` VARCHAR(15) NOT NULL DEFAULT '' COMMENT '省',
`city` VARCHAR(15) NOT NULL DEFAULT '' COMMENT '市',
`district` VARCHAR(15) NOT NULL DEFAULT '' COMMENT '区',
`address` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '地址',
`longitude` float(10, 6) NOT NULL DEFAULT 0 COMMENT '经度',
`latitude` float(10, 6) NOT NULL DEFAULT 0 COMMENT '纬度',
`status` TINYINT NOT NULL DEFAULT 0 COMMENT '0：停用，1：启用',
`create_time` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
`update_time` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
PRIMARY KEY(`device_id`),
UNIQUE KEY(`sim_iccid`)
) ENGINE=INNODB COMMENT='设备表' AUTO_INCREMENT=901030;

# 设备端口表
drop table if exists `device_port`;
create table `device_port`(
`port_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
`device_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '',
`port_no` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '',
`status` TINYINT NOT NULL DEFAULT 0 COMMENT '状态',
`surplus_time` SMALLINT NOT NULL DEFAULT 0 COMMENT '剩余多少分钟',
`surplus_energy` FLOAT(6, 3) NOT NULL DEFAULT 0 COMMENT '剩余电量',
`power` SMALLINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '功率W',
`current_order_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '当前订单号',
PRIMARY KEY(`port_id`),
INDEX(`device_id`, `port_no`),
INDEX(`current_order_id`)
) ENGINE=INNODB COMMENT='设备端口表' AUTO_INCREMENT=901030;

drop table if exists `device_lock`;
create table `device_lock`(
`id` TINYINT UNSIGNED NOT NULL,
PRIMARY KEY(`id`)
) ENGINE=INNODB COMMENT='' AUTO_INCREMENT=1;
insert into `device_lock` (`id`) values (1);

drop table if exists `recycle`;
create table `recycle`(
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
`pk` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '',
`type` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '1:设备',
`symbol` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '标识',
`data` JSON,
`create_time` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
PRIMARY KEY(`id`),
INDEX(`type`, `pk`)
) ENGINE=INNODB COMMENT='回收站' AUTO_INCREMENT=1030;

# 充电价格
drop table if exists `charge_price`;
create table `charge_price`(
`price_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
`user_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '设置套餐的人',
`price_name` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '',
`charge_type` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '计费类型，1：计时，2：计量',
`charge_unit` FLOAT(5, 3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '计费单位',
`max_power` SMALLINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '最大功率',
`critical_power` SMALLINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '临界功率',
`check_time` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '检测时间',
`prepayment` DECIMAL(4, 2) NOT NULL DEFAULT 0 COMMENT '预付费',
`power_class` JSON COMMENT '',
`options` JSON COMMENT '预设套餐',
`create_time` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
`update_time` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
PRIMARY KEY(`price_id`),
INDEX(`user_id`)
) ENGINE=MYISAM COMMENT='充电价格' AUTO_INCREMENT=901030;


drop table if exists `charge_order`;
create table `charge_order`(
`order_id` INT UNSIGNED AUTO_INCREMENT,
`order_no` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '订单号',
`trade_no` VARCHAR(64) NOT NULL DEFAULT "" COMMENT '交易号',
`user_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户id',
`device_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '设备id',
`port_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '端口id',
`port_no` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '端口号',
`charge_unit` FLOAT(5, 3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '计费单位',
`charge_type` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '计费类型，1：计时，2：计量',
`pay_type` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '支付方式，1：余额，2：线上',
`amount` DECIMAL(4,2) NOT NULL DEFAULT 0.00 COMMENT '订单金额',
`quantity` FLOAT(6,2) NOT NULL DEFAULT 0 COMMENT '订单设置充电多少分钟/度',
`consume_amount` DECIMAL(4,2) NOT NULL DEFAULT 0.00 COMMENT '实际消费金额',
`consume_quantity` FLOAT(6,2) NOT NULL DEFAULT 0 COMMENT '使用多少分钟/度',
`avg_power` SMALLINT UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '平均功率',
`max_power` SMALLINT UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '最大功率',
`stop_reason` TINYINT NOT NULL DEFAULT 0 COMMENT '0：充满自停，1：时间/度数-跑满，2：插头断开，3：超功率，-1：手动停止',
`pay_status` TINYINT NOT NULL DEFAULT 0 COMMENT '0:未支付，已支付',
`status` TINYINT NOT NULL DEFAULT 0 COMMENT '0:未支付，1：充电中，2：充电完成，3：充电失败',
`refund_amount` DECIMAL(4,2) NOT NULL DEFAULT 0.00 COMMENT '退款金额',
`refund_status` TINYINT NOT NULL DEFAULT 0 COMMENT '1：等待退款中，2：退款完成',
`create_time` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建订单时间',
`start_time` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '开始充电',
`end_time` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '结束充电',
PRIMARY KEY(`order_id`),
unique key  (`order_no`),
INDEX(`user_id`,`agent_id`,`store_id`)
) ENGINE=INNODB CHARSET=UTF8 COMMENT='充电订单表' AUTO_INCREMENT=901030;







-- ----------------------------
-- create  Table orders
-- ----------------------------

CREATE TABLE `orders` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `curr_id` int (11) unsigned NOT NULL DEFAULT 0 COMMENT '货币类型',
  `trade_type` TINYINT(2) unsigned NOT NULL DEFAULT 10 COMMENT '买卖类型【10 买 20 卖】',
  `curr_abb` VARCHAR (40)  NOT NULL DEFAULT '' COMMENT '货币简称',
  `add_time` int (11) unsigned NOT NULL DEFAULT 0 COMMENT '行为发起时间',
  `total_volume` DECIMAL (17,8) unsigned NOT NULL DEFAULT 0.00000000 COMMENT '总量(本币)',
  `total_volume_btc` DECIMAL (17,8) unsigned NOT NULL DEFAULT 0.00000000 COMMENT '总量(btc)',
  `initial_volume` DECIMAL (17,8) unsigned NOT NULL DEFAULT 0.00000000 COMMENT '消费量(btc)',
  `residual_volume` DECIMAL (17,8) unsigned NOT NULL DEFAULT 0.00000000 COMMENT '剩余量(btc)',
  `price` DECIMAL (17,8) unsigned NOT NULL DEFAULT 0.00000000 COMMENT '货币价格(本币对btc)',
  `fee_money` DECIMAL (17,8) unsigned NOT NULL DEFAULT 0.00000000 COMMENT '手续费',
  `order_status` TINYINT(2) unsigned NOT NULL DEFAULT 10 COMMENT '交易状态【10 发起初始 20 已未完成交易 30 交易完成 】',
  `last_time` int (11) unsigned NOT NULL DEFAULT 0 COMMENT '最后交易时间',
  PRIMARY KEY (`id`),
  CONSTRAINT `orders_curr_set` FOREIGN KEY (`curr_id`) REFERENCES `currency_sets` (`curr_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


-- ----------------------------
-- create  Table order_details
-- ----------------------------

CREATE TABLE `order_details` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '用户订单ID',
  `curr_id` int (11) unsigned NOT NULL DEFAULT 0 COMMENT '货币类型',
  `curr_abb` VARCHAR (40)  NOT NULL DEFAULT '' COMMENT '货币简称',
  `user_id` int (11) unsigned NOT NULL DEFAULT 0 COMMENT '用户ID',
  `user_alias` VARCHAR (40) NOT NULL DEFAULT '' COMMENT '用户全称标识',
  `launch_time` INT (11) unsigned NOT NULL NULL DEFAULT 0 COMMENT '行为发起时间',
  `start_amount` DECIMAL (17,8) NOT NULL DEFAULT 0.00000000 COMMENT '用户最初货币总量（本币）',
  `price_btc` DECIMAL (17,8) unsigned NOT NULL DEFAULT 0.00000000 COMMENT '货币价格（btc）',
  `volume_btc` DECIMAL (17,8) unsigned NOT NULL DEFAULT 0.00000000 COMMENT '交易数量（btc）',
  `fee_money` DECIMAL (17,8) unsigned NOT NULL DEFAULT 0.00000000 COMMENT '完成或者撤回需交手续费 （btc）',
  `fee_money_btc` DECIMAL (17,8) unsigned NOT NULL DEFAULT 0.00000000 COMMENT '手续费 （btc）',
  `net_volume` DECIMAL (17,8) unsigned NOT NULL DEFAULT 0.00000000 COMMENT '最后得到或者花掉得货币（本币）',
  `net_volume_btc` DECIMAL (17,8) unsigned NOT NULL DEFAULT 0.00000000 COMMENT '最后得到或者花掉得货币（btc）',
  `rate` DECIMAL (6,3) unsigned NOT NULL DEFAULT 0.005 COMMENT '手续费率',
  `usd_rate` DECIMAL (6,3) unsigned NOT NULL DEFAULT 0.005 COMMENT 'btc对美元汇率',
  `cny_rate` DECIMAL (6,3) unsigned NOT NULL DEFAULT 0.005 COMMENT 'btc对人民币汇率',
  `end_amount` DECIMAL (17,8) unsigned NOT NULL DEFAULT 0.00000000 COMMENT '操作后货币量（本币）',
  `start_btc` DECIMAL (17,8) unsigned NOT NULL DEFAULT 0.00000000 COMMENT '开始时候btc量',
  `end_btc` DECIMAL (17,8) unsigned NOT NULL DEFAULT 0.00000000 COMMENT '操作后btc量',
  `initial_volume` DECIMAL (17,8) unsigned NOT NULL DEFAULT 0.00000000 COMMENT '消费售量(btc)',
  `residual_volume` DECIMAL (17,8) unsigned NOT NULL DEFAULT 0.00000000 COMMENT '剩余量(btc)',
  `trade_type` TINYINT(2) unsigned NOT NULL DEFAULT 10 COMMENT '操作类型【10 买 20 卖】',
  `order_status` TINYINT(2) unsigned NOT NULL DEFAULT 10 COMMENT '交易状态【10 发起初始 20 已未完成交易 30 交易完成 】',
  `operation` TINYINT(2) unsigned NOT NULL DEFAULT 10 COMMENT '交易状态【10 交易中 20 取消交易】',
  `last_time` int (11) unsigned NOT NULL DEFAULT 0 COMMENT '最后交易时间',
  PRIMARY KEY (`id`),
  KEY `time` (`launch_time`),
  CONSTRAINT `order_details_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  CONSTRAINT `order_details_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `order_details_curr_set` FOREIGN KEY (`curr_id`) REFERENCES `currency_sets` (`curr_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- alter  Table order_details for
-- ----------------------------
ALTER TABLE `order_details`
  ADD COLUMN `curr_abb` VARCHAR(10) NOT NULL DEFAULT '' COMMENT '货币简称' AFTER `curr_id`;
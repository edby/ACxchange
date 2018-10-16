

-- ----------------------------
--  create table xchange_details
-- ----------------------------

DROP TABLE IF EXISTS `xchange_details`;

CREATE TABLE `xchange_details` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `buy_user` int(11) NOT NULL COMMENT '买用户ID',
  `sell_user` int(11) NOT NULL COMMENT '卖用户ID',
  `buy_order` varchar(100)  NOT NULL DEFAULT '' COMMENT '买订单号',
  `sell_order` varchar(100)  NOT NULL DEFAULT '' COMMENT '卖订单号',
  `trade_volume` decimal(20,8) NOT NULL DEFAULT 0.00000000 COMMENT '交易量',
  `buy_volume` decimal(20,8) NOT NULL DEFAULT 0.00000000 COMMENT '买总交易量',
  `sell_volume` decimal(20,8) NOT NULL DEFAULT 0.00000000 COMMENT '卖总交易量',
  `buy_surplus` decimal(20,8) NOT NULL DEFAULT 0.00000000 COMMENT '买剩余量',
  `sell_surplus` decimal(20,8) NOT NULL DEFAULT 0.00000000 COMMENT '卖剩余量',
  `buy_receive` decimal(20,8) NOT NULL DEFAULT 0.00000000 COMMENT '买收到',
  `sell_receive` decimal(20,8) NOT NULL DEFAULT 0.00000000 COMMENT '卖收到',
  `buy_fee` decimal(20,8) NOT NULL DEFAULT 0.00000000 COMMENT '买手续费',
  `sell_fee` decimal(20,8) NOT NULL DEFAULT 0.00000000 COMMENT '卖手续费',
  `buy_rate` decimal(8,4) NOT NULL DEFAULT 0.0000 COMMENT '买手续费费率',
  `sell_rate` decimal(8,4) NOT NULL DEFAULT 0.0000 COMMENT '卖手续费费率',
  `market_name`  varchar(20) NOT NULL DEFAULT '' COMMENT '市场',
  `relationship` TINYINT(2)  NOT NULL DEFAULT 10 COMMENT '发起类型【10.主动，20.被动】参考坐标为买',
  `price` decimal(20,8) NOT NULL DEFAULT 0.00000000 COMMENT '价格',
  `buy_id` INT(11) NOT NULL DEFAULT 0 COMMENT '买ID',
  `sell_id` INT(11) NOT NULL DEFAULT 0 COMMENT '卖ID',
  `tui_qian` decimal(20,8) NOT NULL DEFAULT 0.00000000 COMMENT '退钱/只有买可以退',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

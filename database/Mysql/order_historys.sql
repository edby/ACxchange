-- ----------------------------
-- create  Table order_historys
-- ----------------------------

CREATE TABLE `order_historys` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `account` VARCHAR (60) NOT NULL DEFAULT '' COMMENT '发起用户',
  `category` TINYINT(2) unsigned NOT NULL DEFAULT 10 COMMENT '转账类型【10 move 20 send 30 rec】',
  `curr_abb` VARCHAR (40)  NOT NULL DEFAULT '' COMMENT '货币简称',
  `time` int (11)  NOT NULL DEFAULT 0 COMMENT '发起时间',
  `amount` DECIMAL (17,8) NOT NULL DEFAULT 0 COMMENT '交易金额',
  `otheraccount` VARCHAR (60)  NOT NULL DEFAULT '' COMMENT '被动用户',
  `comment` VARCHAR (255)  NOT NULL DEFAULT '' COMMENT '备注',
  `add_time` int (11)  NOT NULL DEFAULT 0 COMMENT '写入数据库时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- create  Table k_lines
-- ----------------------------

CREATE TABLE `k_lines` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `open` DECIMAL (17,8) unsigned NOT NULL DEFAULT 0 COMMENT '开始价格',
  `low` DECIMAL (17,8) unsigned  NOT NULL DEFAULT 0 COMMENT '最低价格',
  `high` DECIMAL (17,8) unsigned  NOT NULL DEFAULT 0 COMMENT '最高价格',
  `close` DECIMAL (17,8) unsigned  NOT NULL DEFAULT 0 COMMENT '关闭价格',
  `average` DECIMAL (17,8) unsigned NOT NULL DEFAULT 0 COMMENT '平均价格',
  `volume` DECIMAL (17,8) unsigned  NOT NULL DEFAULT 0 COMMENT '交易量',
  `datum_type` TINYINT(2) unsigned NOT NULL DEFAULT 1 COMMENT '基准类型【15 分钟】',
  `curr_id` INT(11) unsigned NOT NULL DEFAULT 0 COMMENT '货币id',
  `curr_abb` VARCHAR (50) NOT NULL DEFAULT '' COMMENT '货币简称',
  `late_time` INT(11) unsigned NOT NULL DEFAULT 0 COMMENT '最近写入时间',
  `datum_time` INT(11) unsigned NOT NULL DEFAULT 0 COMMENT '基准时间 【15 分钟】',
  `add_time` int (11) unsigned NOT NULL DEFAULT 0 COMMENT '写入数据库时间',
  PRIMARY KEY (`id`),
  CONSTRAINT `k_lines_curr_sets` FOREIGN KEY (`curr_id`) REFERENCES `currency_sets` (`curr_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

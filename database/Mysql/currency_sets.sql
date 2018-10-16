

-- ----------------------------
-- alter  Table currency_sets
-- ----------------------------

ALTER TABLE `currency_sets`
  ADD COLUMN `price_cny`  decimal(17,8) UNSIGNED NOT NULL DEFAULT 0.00000000 COMMENT '人民币对应价格' AFTER `curr_abb`,
  ADD COLUMN `price_usd`  decimal(17,8) UNSIGNED NOT NULL DEFAULT 0.00000000 COMMENT '美元对应价格' AFTER `price_cny`,
  ADD COLUMN `price_btc`  decimal(17,8) UNSIGNED NOT NULL DEFAULT 0.00000000 COMMENT '比特币对应价格' AFTER `price_usd`,
  ADD COLUMN `24h_volume_usd`  decimal(17,2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '二十四小时成交量对美元' AFTER `price_btc`,
  ADD COLUMN `market_cap_usd`  decimal(17,2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '' AFTER `24h_volume_usd`,
  ADD COLUMN `available_supply`  decimal(17,2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '' AFTER `market_cap_usd`,
  ADD COLUMN `total_supply`  decimal(17,2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '' AFTER `available_supply`,
  ADD COLUMN `percent_change_1h`  decimal(5,5)  NOT NULL DEFAULT 0.00 COMMENT '一小时涨幅' AFTER `total_supply`,
  ADD COLUMN `percent_change_24h`  decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT '二十四小时涨幅' AFTER `percent_change_1h`,
  ADD COLUMN `percent_change_7d`  decimal(5,2)  NOT NULL DEFAULT 0.00 COMMENT '七天小时涨幅' AFTER `percent_change_24h`,
  ADD COLUMN `last_updated`  INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '最新更新时间' AFTER `percent_change_7d`;

-- ----------------------------
-- alter  Table currency_sets
-- ----------------------------
ALTER TABLE `currency_sets` ADD UNIQUE KEY  name_abb (`curr_abb`,`curr_name`);


-- ----------------------------
-- alter  Table currency_sets
-- ----------------------------
ALTER TABLE `currency_sets`
  ADD INDEX price_cny ( `price_cny` ) ,
  ADD INDEX price_usd ( `price_usd` ) ,
  ADD INDEX price_btc ( `price_btc` ) ;
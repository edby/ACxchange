

-- ----------------------------
-- alter  Table user_currs for `users`
-- ----------------------------

ALTER TABLE `user_currs`
  ADD COLUMN `in_trade`  decimal(17,8) UNSIGNED NOT NULL DEFAULT 0.00000000 COMMENT '在交易的总额' AFTER `curr_abb`;
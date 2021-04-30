ALTER TABLE `wp_jiangqie_post_view` ADD `time` INT NOT NULL DEFAULT '0' COMMENT '时间' AFTER `post_id`;
ALTER TABLE `wp_jiangqie_post_like` ADD `time` INT NOT NULL DEFAULT '0' COMMENT '时间' AFTER `post_id`;
ALTER TABLE `wp_jiangqie_post_favorite` ADD `time` INT NOT NULL DEFAULT '0' COMMENT '时间' AFTER `post_id`;
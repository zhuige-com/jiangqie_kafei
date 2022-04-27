<?php

/*
 * 酱茄小程序开源版
 * Author: 酱茄
 * Help document: https://www.jiangqie.com/docs/kaiyuan/id1
 * github: https://github.com/longwenjunjie/jiangqie_kafei
 * gitee: https://gitee.com/longwenjunj/jiangqie_kafei
 * Copyright ️© 2020-2022 www.jiangqie.com All rights reserved.
 */

class JiangQie_API_Activator
{
    public static function activate()
    {
        global $wpdb;

        $charset_collate = '';
        if (!empty($wpdb->charset)) {
            $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
        }

        if (!empty($wpdb->collate)) {
            $charset_collate .= " COLLATE {$wpdb->collate}";
        }

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $table_post_view = $wpdb->prefix . 'jiangqie_post_view';
        $sql = "CREATE TABLE IF NOT EXISTS `$table_post_view` (
          `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
          `user_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '用户ID',
          `post_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '文章ID',
          `time` int(11) NOT NULL DEFAULT '0' COMMENT '时间',
          PRIMARY KEY (`id`)
        ) $charset_collate;";
        dbDelta($sql);

        $table_post_like = $wpdb->prefix . 'jiangqie_post_like';
        $sql = "CREATE TABLE IF NOT EXISTS `$table_post_like` (
          `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
          `user_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '用户ID',
          `post_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '文章ID',
          `time` int(11) NOT NULL DEFAULT '0' COMMENT '时间',
          PRIMARY KEY (`id`)
        ) $charset_collate;";
        dbDelta($sql);

        $table_post_favorite = $wpdb->prefix . 'jiangqie_post_favorite';
        $sql = "CREATE TABLE IF NOT EXISTS `$table_post_favorite` (
          `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
          `user_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '用户ID',
          `post_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '文章ID',
          `time` int(11) NOT NULL DEFAULT '0' COMMENT '时间',
          PRIMARY KEY (`id`)
        ) $charset_collate;";
        dbDelta($sql);

        //搜索
        $table_post_search = $wpdb->prefix . 'jiangqie_post_search';
        $sql = "CREATE TABLE IF NOT EXISTS `$table_post_search` (
          `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
          `search` varchar(250) NOT NULL DEFAULT '' COMMENT '搜索关键字',
          `times` int(11) NOT NULL DEFAULT 0 COMMENT '次数',
          PRIMARY KEY (`id`)
        ) $charset_collate;";
        dbDelta($sql);
    }
}

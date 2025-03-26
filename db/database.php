<?php
if (!defined('ABSPATH')) {
    exit;
}

// Création des tables
function wp_annotation_create_tables() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'reviews';
    $table_name_replies = $wpdb->prefix . 'reviews_replies';
    $charset_collate = $wpdb->get_charset_collate();

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        $sql = "CREATE TABLE $table_name (
            id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            page_id INT NOT NULL,
            position_x INT NOT NULL,
            position_y INT NOT NULL,
            commentaire TEXT NOT NULL,
            timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
            statut ENUM('non résolu', 'résolu') DEFAULT 'non résolu',
            device ENUM('laptop', 'tablet', 'mobile') DEFAULT 'laptop',
            user_id BIGINT(20) UNSIGNED NOT NULL,
            screenshot_url TEXT
        ) $charset_collate;";
        
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name_replies'") != $table_name_replies) {
        $sql_replies = "CREATE TABLE $table_name_replies (
            id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            comment_id BIGINT(20) UNSIGNED NOT NULL,
            user_id BIGINT(20) UNSIGNED NOT NULL,
            commentaire TEXT NOT NULL,
            timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
            file_path TEXT
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql_replies);
    }
}
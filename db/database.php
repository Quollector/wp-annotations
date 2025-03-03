<?php
if (!defined('ABSPATH')) {
    exit;
}

// Création de la table
function wp_annotation_install() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'reviews';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
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
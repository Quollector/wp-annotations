<?php
// Création des tables
function wp_annotation_create_tables() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'reviews';
    $table_name_replies = $wpdb->prefix . 'reviews_replies';
    $charset_collate = $wpdb->get_charset_collate();

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    $tables = [
        $table_name => [
            'id' => 'BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'page_id' => 'INT NOT NULL',
            'position_x' => 'INT NOT NULL',
            'position_y' => 'INT NOT NULL',
            'commentaire' => 'TEXT NOT NULL',
            'timestamp' => "DATETIME DEFAULT CURRENT_TIMESTAMP",
            'statut' => "ENUM('non résolu', 'résolu') DEFAULT 'non résolu'",
            'device' => "ENUM('laptop', 'tablet', 'mobile') DEFAULT 'laptop'",
            'user_id' => 'BIGINT(20) UNSIGNED NOT NULL',
            'screenshot_url' => 'TEXT'
        ],
        $table_name_replies => [
            'id' => 'BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'comment_id' => 'BIGINT(20) UNSIGNED NOT NULL',
            'user_id' => 'BIGINT(20) UNSIGNED NOT NULL',
            'commentaire' => 'TEXT NOT NULL',
            'timestamp' => "DATETIME DEFAULT CURRENT_TIMESTAMP",
            'file_path' => 'TEXT'
        ]
    ];

    foreach ($tables as $table => $columns) {
        if ($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {
            $columns_sql = implode(", ", array_map(fn($col, $type) => "$col $type", array_keys($columns), $columns));
            $sql = "CREATE TABLE $table ($columns_sql) $charset_collate;";
            dbDelta($sql);
        } else {
            $existing_columns = $wpdb->get_results("SHOW COLUMNS FROM $table", ARRAY_A);
            $existing_columns = array_column($existing_columns, 'Field');

            foreach ($columns as $col => $type) {
                if (!in_array($col, $existing_columns)) {
                    $wpdb->query("ALTER TABLE $table ADD COLUMN $col $type;");
                }
            }
        }
    }
}
<?php
if (!defined('ABSPATH')) {
    exit;
}

// Ajout d'une page d'options dans l'admin
function wp_annotation_admin_menu() {
    add_menu_page('WP Annotations', 'Annotations', 'manage_options', 'wp-annotations', 'wp_annotation_settings_page');
}
add_action('admin_menu', 'wp_annotation_admin_menu');

// Page d'options (frontend - admin)
function wp_annotation_settings_page() {
    if ( file_exists( WP_ANNOTATION_PATH . 'views/options.php' ) ) {
        include WP_ANNOTATION_PATH . 'views/options.php';
    }
}
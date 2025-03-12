<?php
if (!defined('ABSPATH')) {
    exit;
}

// Ajout d'une page d'options dans l'admin
function wp_annotation_admin_menu() {
    add_submenu_page(
        'options-general.php',          // Slug du menu parent (Outils)
        'WP Annotations',     // Titre de la page
        'Annotations',        // Texte du menu
        'manage_options',     // Capacité requise
        'wp-annotations',     // Slug unique pour cette page
        'wp_annotation_settings_page' // Fonction de rappel pour afficher la page
    );
}
add_action('admin_menu', 'wp_annotation_admin_menu');

// Page d'options (frontend - admin)
function wp_annotation_settings_page() {
    if ( file_exists( WP_ANNOTATION_PATH . 'views/options.php' ) ) {
        include WP_ANNOTATION_PATH . 'views/options.php';
    }
}
<?php
/**
 * Plugin Name: WP Annotations Plugin
 * Description: Plugin d'annotation
 * Version: 1.2.6
 * Author: Quentin Lequenne
 */

// Enregistrement des paramètres
function wp_annotation_register_settings() {
    register_setting('wp_annotation_options', 'wp_annotation_users');
    register_setting('wp_annotation_options', 'wp_annotation_enabled');
    register_setting('wp_annotation_options', 'wp_annotation_color');
    register_setting('wp_annotation_options', 'wp_annotation_quality');
    register_setting('wp_annotation_options', 'wp_annotation_smtp_mail');
    register_setting('wp_annotation_options', 'wp_annotation_smtp_user');
    register_setting('wp_annotation_options', 'wp_annotation_smtp_password');
    register_setting('wp_annotation_options', 'wp_annotation_smtp_from_name');
    register_setting('wp_annotation_options', 'wp_annotation_smtp_from_email');
    register_setting('wp_annotation_options', 'wp_annotation_smtp_valid');
}
add_action('admin_init', 'wp_annotation_register_settings');

// Constantes
define('WP_ANNOTATION_PATH', plugin_dir_path( __FILE__ ));
define('WP_ANNOTATION_URL', plugin_dir_url( __FILE__ ));
define('WP_ANNOTATION_COLORS', [
    'blue' => [
        'main' => '#3C77A1',
        'main-rgb' => '60, 119, 161',
        'sombre' => '#062F4D',
        'sombre-rgb' => '6, 47, 77',
        'clair' => '#1391EB',
        'clair-rgb' => '19, 145, 235',
        'alt' => '#0D619E',
        'alt-rgb' => '13, 97, 158',
    ],
    'red' => [
        'main' => '#A31F1A',
        'main-rgb' => '163, 31, 26',
        'sombre' => '#57100E',
        'sombre-rgb' => '87, 16, 14',
        'clair' => '#E62B25',
        'clair-rgb' => '230, 43, 37',
        'alt' => '#A8201B',
        'alt-rgb' => '168, 32, 27',
    ],
    'green' => [
        'main' => '#0EA31A',
        'main-rgb' => '14, 163, 26',
        'sombre' => '#06420B',
        'sombre-rgb' => '6, 66, 11',
        'clair' => '#13D123',
        'clair-rgb' => '19, 209, 35',
        'alt' => '#0D9419',
        'alt-rgb' => '13, 148, 25',
    ],
    'orange' => [
        'main' => '#A86608',
        'main-rgb' => '168, 102, 8',
        'sombre' => '#422803',
        'sombre-rgb' => '66, 40, 3',
        'clair' => '#FA970C',
        'clair-rgb' => '250, 151, 12',
        'alt' => '#A86608',
        'alt-rgb' => '168, 102, 8',
    ],
    'purple' => [
        'main' => '#7A08A3',
        'main-rgb' => '122, 8, 163',
        'sombre' => '#320342',
        'sombre-rgb' => '50, 3, 66',
        'clair' => '#AB0BE6',
        'clair-rgb' => '171, 11, 230',
        'alt' => '#7E08A8',
        'alt-rgb' => '126, 8, 168',
    ]
]);

// Styles / scripts
function wp_annotations_enqueue_assets() {
    wp_enqueue_style(
        'wp-annotations-style', 
        WP_ANNOTATION_URL . 'assets/css/style.min.css', 
        [],
        filemtime(WP_ANNOTATION_PATH . 'assets/css/style.min.css')
    );

    wp_enqueue_script(
        'wp-annotations-script', 
        WP_ANNOTATION_URL . 'assets/scripts/script.js', 
        ['jquery'],
        filemtime(WP_ANNOTATION_PATH . 'assets/scripts/script.js'),
        true 
    );

    wp_enqueue_script(
        'html2canvas', 
        'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js', 
        [], 
        '1.4.1', 
        true
    );

    // Assure-toi que tu transmets un tableau
    wp_localize_script('wp-annotations-script', 'ajaxurl', array(
        'url' => admin_url('admin-ajax.php')
    ));
    
    wp_localize_script('wp-annotations-script', 'datas', array(
        'quality' => get_option('wp_annotation_quality', '0.7')
    ));
}
add_action('wp_enqueue_scripts', 'wp_annotations_enqueue_assets');

// Functions
if ( file_exists( WP_ANNOTATION_PATH . 'assets/functions.php' )) {
    include WP_ANNOTATION_PATH . 'assets/functions.php';
}

// Database
if ( file_exists( WP_ANNOTATION_PATH . 'db/database.php' )) {
    include WP_ANNOTATION_PATH . 'db/database.php';
}
add_action('init', 'wp_annotation_create_tables');

// Dashboard
if ( file_exists( WP_ANNOTATION_PATH . 'admin/admin.php' )) {
    include WP_ANNOTATION_PATH . 'admin/admin.php';
}

// Frontend
if ( file_exists( WP_ANNOTATION_PATH . 'public/public.php' )) {
    include WP_ANNOTATION_PATH . 'public/public.php';
}

// AJAX
if ( file_exists( WP_ANNOTATION_PATH . 'db/ajax.php' ) ) {
    include WP_ANNOTATION_PATH . 'db/ajax.php';
}

// OPTIONS LINK
function wp_annotations_plugin_action_links($links, $file){
    if ($file == plugin_basename(__FILE__)) {
        $link = '<a href="'.esc_url(get_admin_url()).'options-general.php?page=wp-annotations">Réglages</a>';
        array_unshift($links, $link);
    }

    return $links;
}
add_filter('plugin_action_links', 'wp_annotations_plugin_action_links', 10, 2);
<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    die;
}

delete_option( 'wp_annotation_users' );
delete_option( 'wp_annotation_enabled' );
delete_option( 'wp_annotation_color' );
delete_option( 'wp_annotation_smtp_mail' );
delete_option( 'wp_annotation_smtp_user' );
delete_option( 'wp_annotation_smtp_password' );
delete_option( 'wp_annotation_smtp_from_name' );
delete_option( 'wp_annotation_smtp_from_email' );
delete_option( 'wp_annotation_smtp_valid' );

global $wpdb;

$table_name = $wpdb->prefix . 'reviews';
$table_name_replies = $wpdb->prefix . 'reviews_replies';

$wpdb->query("DROP TABLE IF EXISTS $table_name");
$wpdb->query("DROP TABLE IF EXISTS $table_name_replies");

$screenshots_dir = plugin_dir_url( __FILE__ ) . 'assets/images/screenshots/';
$replies_dir = plugin_dir_url( __FILE__ ) . 'assets/images/replies/';

if ( is_dir( $screenshots_dir ) ) {
    $files = glob( $screenshots_dir . '*' ); 
    foreach ( $files as $file ) {
        if ( is_file( $file ) ) {
            unlink( $file );
        }
    }
}

if ( is_dir( $replies_dir ) ) {
    $files = glob( $replies_dir . '*' ); 
    foreach ( $files as $file ) {
        if ( is_file( $file ) ) {
            unlink( $file );
        }
    }
}
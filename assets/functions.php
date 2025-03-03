<?php
if (!defined('ABSPATH')) {
    exit;
}

function is_user_allowed_for_annotations() {
    $current_user = wp_get_current_user();

    $allowed_users = get_option('wp_annotation_users', []);

    return in_array($current_user->ID, (array) $allowed_users);
}
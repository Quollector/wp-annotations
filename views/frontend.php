<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<!-- Lightbox -->
<?php  
if ( file_exists( WP_ANNOTATION_PATH . 'views/frontend/lightbox.php' ) ) {
    include WP_ANNOTATION_PATH . 'views/frontend/lightbox.php';
}
?>

<!-- Switch -->
<?php  
if ( file_exists( WP_ANNOTATION_PATH . 'views/frontend/switch.php' ) ) {
    include WP_ANNOTATION_PATH . 'views/frontend/switch.php';
}
?>

<!-- Dashboard -->
<?php  
if ( file_exists( WP_ANNOTATION_PATH . 'views/frontend/dashboard.php' ) ) {
    include WP_ANNOTATION_PATH . 'views/frontend/dashboard.php';
}
?>

<!-- Comments layout -->
<div id="wp-annotations--comments-layout" class="wp-annotations--comments-layout">
    <?php  
    if ( file_exists( WP_ANNOTATION_PATH . 'views/frontend/modal.php' ) ) {
        include WP_ANNOTATION_PATH . 'views/frontend/modal.php';
    }
    ?>
</div>

<!-- Notices -->
<div id="wp-annotations--notices" class="wp-annotations--notices"></div>
<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wp-annotations--dashboard" id="wp-annotations--dashboard">
    <div class="wp-annotations--dashboard__wrapper">
        <div class="wp-annotations--dashboard__ajax">
            <span class="loader-ajax"></span>
        </div>
        <div id="wp-annotations--refresh-box" class="wp-annotations--refresh-box">
            <?php  
            if ( file_exists( WP_ANNOTATION_PATH . 'views/frontend/comments-box.php' ) ) {
                include WP_ANNOTATION_PATH . 'views/frontend/comments-box.php';
            }
            ?>
        </div>
    </div>
</div>
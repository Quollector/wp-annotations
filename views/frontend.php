<!-- Replies box -->
<?php  
if ( file_exists( WP_ANNOTATION_PATH . 'views/frontend/replies/replies.php' ) ) {
    include WP_ANNOTATION_PATH . 'views/frontend/replies/replies.php';
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
<div id="wp-annotations--notices" class="wp-annotations--notices">
    <div class="wp-annotations--notice wp-annotations--notice__success">
        <span>
            <svg width="800px" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4 12.6111L8.92308 17.5L20 6.5" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </span>
        <p class="wp-annotations--notice__message"></p>
    </div>
    <div class="wp-annotations--notice wp-annotations--notice__error">
        <span>
            <svg width="800px" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4 4L20 20" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M20 4L4 20" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </span>
        <p class="wp-annotations--notice__message">Lorem ipsum dolor sit amet</p>
    </div>
</div>
<div id="wp-annotations--replies" class="wp-annotations--replies">
    <div class="wp-annotations--replies__ajax">
        <span class="loader-ajax"></span>
    </div>
    <div class="wp-annotations--replies__wrapper" id="wp-annotations-replies-display">        
        <?php  
        if ( file_exists( WP_ANNOTATION_PATH . 'views/frontend/replies/replies-box.php' ) ) {
            include WP_ANNOTATION_PATH . 'views/frontend/replies/replies-box.php';
        }
        ?>
    </div>
</div>
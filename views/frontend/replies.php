<div id="wp-annotations__replies" class="wp-annotations__replies">
    <div class="wp-annotations__replies--ajax">
        <span class="loader-ajax"></span>
    </div>
    <div class="wp-annotations__replies--wrapper" id="wp-annotations-replies-display">        
        <?php  
        if ( file_exists( WP_ANNOTATION_PATH . 'views/frontend/replies/replies-box.php' ) ) {
            include WP_ANNOTATION_PATH . 'views/frontend/replies/replies-box.php';
        }
        ?>
    </div>
</div>
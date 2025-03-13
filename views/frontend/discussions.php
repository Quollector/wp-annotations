<div id="wp-annotations--discussions" class="wp-annotations--discussions">
    <div class="wp-annotations--discussions__ajax">
        <span class="loader-ajax"></span>
    </div>
    <div class="wp-annotations--discussions__wrapper" id="wp-annotations-discussions-display">        
        <?php  
        if ( file_exists( WP_ANNOTATION_PATH . 'views/frontend/discussions-box.php' ) ) {
            include WP_ANNOTATION_PATH . 'views/frontend/discussions-box.php';
        }
        ?>
    </div>
</div>
<?php if ( isset( $comment_data )): ?>
<div class="reply-box" data-user-id="<?= $comment_data['user_id'] ?>" data-comment-id="<?= $comment_data['id'] ?>">
    <div class="reply-box__ajax">
        <span class="loader-ajax"></span>
    </div>
    <div class="reply-box__wrapper" id="reply-box-content">        
        <?php  
        if ( file_exists( WP_ANNOTATION_PATH . 'views/frontend/replies/replies-box-content.php' ) ) {
            include WP_ANNOTATION_PATH . 'views/frontend/replies/replies-box-content.php';
        }
        ?>
    </div>
</div>
<?php endif; ?>
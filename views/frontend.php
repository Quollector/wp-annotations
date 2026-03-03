<!-- Replies box -->
<?php  
if ( file_exists( WP_ANNOTATION_PATH . 'views/frontend/replies.php' ) ) {
    include WP_ANNOTATION_PATH . 'views/frontend/replies.php';
}
?>

<!-- Lightbox -->
<div id="wp-annotations--lightbox" class="wp-annotations--lightbox">
    <div class="wp-annotations--lightbox__wrapper">
        <div class="close-light-button">
            <img src="<?= WP_ANNOTATION_URL . 'assets/images/icons/close.svg' ?>" alt="Fermer" title="Fermer">
        </div>
        <img src="" class="lightbox-img">
    </div>
</div>

<!-- Switches -->
<div class="wp-annotations__actions">
    <button class="wp-annotations__actions--bubble ann-dash" id="wp-annotations--dash-bubble">
        <img src="<?= WP_ANNOTATION_URL ?>assets/images/icons/annotation.svg" alt="Tableau de bord" title="Tableau de bord" class="comment">
        <img src="<?= WP_ANNOTATION_URL ?>assets/images/icons/close.svg" alt="Fermer" title="Fermer" class="browse">
    </button>
    <button class="wp-annotations__actions--bubble ann-switch" id="wp-annotations--switch-bubble">
        <img src="<?= WP_ANNOTATION_URL ?>assets/images/icons/pen.svg" alt="Commenter" title="Commenter" class="comment">
        <img src="<?= WP_ANNOTATION_URL ?>assets/images/icons/unpen.svg" alt="Naviguer" title="Naviguer" class="browse">
    </button>
</div>

<!-- Dashboard -->
<?php  
if ( file_exists( WP_ANNOTATION_PATH . 'views/frontend/dashboard.php' ) ) {
    include WP_ANNOTATION_PATH . 'views/frontend/dashboard.php';
}
?>

<!-- Comments layout -->
<div id="wp-annotations--comments-layout" class="wp-annotations--comments-layout">
    <div class="wp-annotations__modal" data-position-x="0" data-position-y="0" data-device="laptop">
        <svg width="118" height="118" viewBox="0 0 118 118" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="59" cy="59" r="40" fill="white" class="modal-fill"/>
            <circle cx="59" cy="59" r="56.5" stroke="white" stroke-width="5" class="modal-stroke"/>
        </svg>
    </div>

    <form id="wp-annotation-form" class="mention-list-parent" data-page-id="<?= get_the_ID() ?>" data-user-id="<?= get_current_user_id() ?>">
        <textarea name="comment" placeholder="Ajouter un commentaire..." rows="3" style="width: 100%;"></textarea>
        <div id="mention-list-main" class="mention-list-main mention-list-box">
            <div class="mention-list-main__wrapper">
                <?php foreach(get_wp_annotations_users_by_name() as $id => $user): if($id != get_current_user_id()): ?>
                    <div class="mention-list-main__item mention-list-item" data-user-id="<?= $id ?>" data-user-name="<?= $user ?>">@<?= $user ?></div>        
                <?php endif; endforeach; ?>
            </div>
        </div>
        
        <?php if( check_user_role() ): ?>
            <label class="client-visible">
                <input type="checkbox" name="client_visible" value="1"> Visible par le client
            </label>
        <?php else: ?>
            <input type="hidden" name="client_visible" value="<?= check_user_role() ? 0 : 1 ?>">
        <?php endif; ?>

        <div class="submit-box">
            <button type="reset">Annuler</button>
            <button type="submit">Envoyer</button>
        </div>
    </form>
</div>

<!-- Notices -->
<div id="wp-annotations--notices" class="wp-annotations--notices"></div>
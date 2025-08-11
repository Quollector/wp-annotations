<div 
id="wp-annotations--modal" 
class="wp-annotations--modal" 
data-position-x="0" 
data-position-y="0" 
data-device="laptop"
data-page-id="<?= get_the_ID() ?>"
data-user-id="<?= get_current_user_id() ?>"
>
    <div class="modal-marker">
        <img src="<?= WP_ANNOTATION_URL . 'assets/images/icons/marker.svg' ?>" alt="" class="">
    </div>
    <form id="wp-annotation-form">
        <textarea name="comment" placeholder="Ajouter un commentaire..." rows="3" style="width: 100%;"></textarea>
        <div id="mention-list-main" class="mention-list-main">
            <div class="mention-list-main__wrapper">
                <?php foreach(get_wp_annotations_users_by_name() as $id => $user): if($id != get_current_user_id()): ?>
                    <div class="mention-list-main__item" data-user-id="<?= $id ?>" data-user-name="<?= $user ?>">@<?= $user ?></div>        
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
<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

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
        <div class="submit-box">
            <button type="submit">Envoyer</button>
        </div>
    </form>
</div>
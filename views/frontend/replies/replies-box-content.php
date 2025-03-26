<?php 
if( isset($new_comment_data) ){
    $comment_data = $new_comment_data;
}

$comments_list = getAllReplies($comment_data['id']);

$interface_color = get_option('wp_annotation_color', 'blue');

$targets_email = [$comment_data['user_id'] != get_current_user_id() ? $comment_data['user_id'] : ''];

?>
<div class="reply-box__header">
    <div class="dot">
        <?= $comment_data['id'] ?>
    </div>
    <h5><?= get_userdata($comment_data['user_id'])->display_name ?></h5>
    <span><?= date('d.m.Y', strtotime($comment_data['timestamp'])) ?></span>
    <div class="device">
        <?php if( $comment_data['device'] === 'laptop' ) : ?>
            <svg width="800px" title="laptop" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M10 18H14M7.20003 3H16.8C17.9201 3 18.4802 3 18.908 3.21799C19.2843 3.40973 19.5903 3.71569 19.782 4.09202C20 4.51984 20 5.0799 20 6.2V11.8C20 12.9201 20 13.4802 19.782 13.908C19.5903 14.2843 19.2843 14.5903 18.908 14.782C18.4802 15 17.9201 15 16.8 15H7.20003C6.07992 15 5.51987 15 5.09205 14.782C4.71572 14.5903 4.40976 14.2843 4.21801 13.908C4.00003 13.4802 4.00003 12.9201 4.00003 11.8V6.2C4.00003 5.0799 4.00003 4.51984 4.21801 4.09202C4.40976 3.71569 4.71572 3.40973 5.09205 3.21799C5.51987 3 6.07992 3 7.20003 3ZM4.58888 21H19.4112C20.2684 21 20.697 21 20.9551 20.8195C21.1805 20.6618 21.3311 20.4183 21.3713 20.1462C21.4173 19.8345 21.2256 19.4512 20.8423 18.6845L20.3267 17.6534C19.8451 16.6902 19.6043 16.2086 19.2451 15.8567C18.9274 15.5456 18.5445 15.309 18.1241 15.164C17.6488 15 17.1103 15 16.0335 15H7.96659C6.88972 15 6.35128 15 5.87592 15.164C5.45554 15.309 5.07266 15.5456 4.75497 15.8567C4.39573 16.2086 4.15493 16.6902 3.67334 17.6534L3.1578 18.6845C2.77444 19.4512 2.58276 19.8345 2.6288 20.1462C2.669 20.4183 2.81952 20.6618 3.04492 20.8195C3.30306 21 3.73166 21 4.58888 21Z" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        <?php elseif( $comment_data['device'] === 'tablet' ) : ?>
            <svg width="800px" title="tablet" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9 3V4.4C9 4.96005 9 5.24008 9.10899 5.45399C9.20487 5.64215 9.35785 5.79513 9.54601 5.89101C9.75992 6 10.0399 6 10.6 6H13.4C13.9601 6 14.2401 6 14.454 5.89101C14.6422 5.79513 14.7951 5.64215 14.891 5.45399C15 5.24008 15 4.96005 15 4.4V3M8.2 21H15.8C16.9201 21 17.4802 21 17.908 20.782C18.2843 20.5903 18.5903 20.2843 18.782 19.908C19 19.4802 19 18.9201 19 17.8V6.2C19 5.0799 19 4.51984 18.782 4.09202C18.5903 3.71569 18.2843 3.40973 17.908 3.21799C17.4802 3 16.9201 3 15.8 3H8.2C7.0799 3 6.51984 3 6.09202 3.21799C5.71569 3.40973 5.40973 3.71569 5.21799 4.09202C5 4.51984 5 5.07989 5 6.2V17.8C5 18.9201 5 19.4802 5.21799 19.908C5.40973 20.2843 5.71569 20.5903 6.09202 20.782C6.51984 21 7.07989 21 8.2 21Z" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        <?php elseif( $comment_data['device'] === 'mobile' ) : ?>
            <svg fill="#000000" title="mobile" width="800px" height="800px" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" class="fill">
                <path d="M12.25 0h-8.5A1.25 1.25 0 0 0 2.5 1.25v13.5A1.25 1.25 0 0 0 3.75 16h8.5a1.25 1.25 0 0 0 1.25-1.25V1.25A1.25 1.25 0 0 0 12.25 0zm0 14.75h-8.5V1.25h8.5z"/>
                <ellipse cx="8" cy="12.75" rx=".8" ry=".75"/>
            </svg>
        <?php endif; ?>
    </div>
    <div class="buttons">
        <button class="close-replies" title="Fermer">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                <path d="M18.3 5.71a.996.996 0 0 0-1.41 0L12 10.59L7.11 5.7A.996.996 0 1 0 5.7 7.11L10.59 12L5.7 16.89a.996.996 0 1 0 1.41 1.41L12 13.41l4.89 4.89a.996.996 0 1 0 1.41-1.41L13.41 12l4.89-4.89c.38-.38.38-1.02 0-1.4"/>
            </svg>
        </button>
    </div>
</div>
<div class="reply-box__comment">
    <?= $comment_data['commentaire'] ?>
</div>
<?php if( !empty( $comments_list ) ): ?>
    <div class="reply-box__replies">
        <?php foreach( $comments_list as $comment ): 
                if ($comment->user_id != get_current_user_id() && !in_array($comment->user_id, $targets_email)):
                    $targets_email[] =  $comment->user_id; 
                endif;
            ?>
            <div class="reply-item" data-id="<?= $comment->id ?>">
                <div class="reply-item__header">
                    <div class="reply-item__header--user">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="16" viewBox="0 0 15 16">
                            <path d="M6 12.5a.47.47 0 0 1-.35-.15l-4.5-4.5C1.06 7.76 1 7.63 1 7.5s.05-.26.15-.35l4.5-4.5c.2-.2.51-.2.71 0s.2.51 0 .71L2.21 7.5l4.15 4.15c.2.2.2.51 0 .71c-.1.1-.23.15-.35.15Z"/>
                            <path d="M13.5 14c-.28 0-.5-.22-.5-.5v-3A2.5 2.5 0 0 0 10.5 8H2.7c-.28 0-.5-.22-.5-.5s.22-.5.5-.5h7.8c1.93 0 3.5 1.57 3.5 3.5v3c0 .28-.22.5-.5.5"/>
                        </svg>
                        <h6><?= get_userdata($comment->user_id)->display_name ?></h6>
                    </div>
                    <div class="reply-item__header--infos">
                        <span>
                            <?= date('d.m.Y', strtotime($comment->timestamp)) ?>
                        </span>
                        
                        <?php if ($comment->user_id == get_current_user_id()) : ?>
                        <div class="buttons">
                            <button class="delete" title="Effacer">
                                <svg width="800px" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M20.5001 6H3.5" stroke="<?= WP_ANNOTATION_COLORS[$interface_color]['sombre'] ?>" stroke-width="1.5" stroke-linecap="round"/>
                                    <path d="M18.8332 8.5L18.3732 15.3991C18.1962 18.054 18.1077 19.3815 17.2427 20.1907C16.3777 21 15.0473 21 12.3865 21H11.6132C8.95235 21 7.62195 21 6.75694 20.1907C5.89194 19.3815 5.80344 18.054 5.62644 15.3991L5.1665 8.5" stroke="<?= WP_ANNOTATION_COLORS[$interface_color]['sombre'] ?>" stroke-width="1.5" stroke-linecap="round"/>
                                    <path d="M9.5 11L10 16" stroke="<?= WP_ANNOTATION_COLORS[$interface_color]['sombre'] ?>" stroke-width="1.5" stroke-linecap="round"/>
                                    <path d="M14.5 11L14 16" stroke="<?= WP_ANNOTATION_COLORS[$interface_color]['sombre'] ?>" stroke-width="1.5" stroke-linecap="round"/>
                                    <path d="M6.5 6C6.55588 6 6.58382 6 6.60915 5.99936C7.43259 5.97849 8.15902 5.45491 8.43922 4.68032C8.44784 4.65649 8.45667 4.62999 8.47434 4.57697L8.57143 4.28571C8.65431 4.03708 8.69575 3.91276 8.75071 3.8072C8.97001 3.38607 9.37574 3.09364 9.84461 3.01877C9.96213 3 10.0932 3 10.3553 3H13.6447C13.9068 3 14.0379 3 14.1554 3.01877C14.6243 3.09364 15.03 3.38607 15.2493 3.8072C15.3043 3.91276 15.3457 4.03708 15.4286 4.28571L15.5257 4.57697C15.5433 4.62992 15.5522 4.65651 15.5608 4.68032C15.841 5.45491 16.5674 5.97849 17.3909 5.99936C17.4162 6 17.4441 6 17.5 6" stroke="<?= WP_ANNOTATION_COLORS[$interface_color]['sombre'] ?>" stroke-width="1.5"/>
                                </svg>
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="reply-item__content">
                    <?= formatNotificationsComment($comment->commentaire) ?>
                </div>
                <div class="reply-item__image">
                    <div class="expend">
                        <img src="<?= WP_ANNOTATION_URL . 'assets/images/icons/expend.svg' ?>" alt="" class="">
                    </div>
                    <img src="<?= WP_ANNOTATION_URL . 'assets/images/replies/' . $comment->file_path ?>" alt="" class="src-img">
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<form class="reply-box__form" id="reply-box-form">
    <?php foreach($targets_email as $email_id): ?>
        <input type="hidden" name="targets_email[]" value="<?= $email_id ?>">
    <?php endforeach; ?>
    <textarea name="comment" id="comment" placeholder="Répondre" rows="5"></textarea>
    <div id="mention-list" class="mention-list">
        <div class="mention-list__wrapper">
            <?php foreach(get_wp_annotations_users_by_name() as $id => $user): if($id != get_current_user_id()): ?>
                <div class="mention-list__item" data-user-id="<?= $id ?>" data-user-name="<?= $user ?>">@<?= $user ?></div>        
            <?php endif; endforeach; ?>
        </div>
    </div>
    
    <div class="file-input">
        <input type="file" name="reply-file" accept="image/*">
        <div class="file-input__front">
            <div class="unfiled">
                <div class="unfiled__wrap">
                    Ajouter une image <iconify-icon icon="iconamoon:file-add-light"></iconify-icon>
                </div>
            </div>
            <div class="filed">
                <div class="filed__wrap">
                    <div class="text">test.jpeg</div>
                    <div class="clear"><iconify-icon icon="ph:file-x-duotone"></iconify-icon></iconify-icon></div>
                </div>
            </div>
        </div>
    </div>

    <label>
        <input type="checkbox" name="email" value="1" checked> Notifier par courriel
    </label>
    <button type="submit">Envoyer</button>
</form>
<?php
if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$table_name = $wpdb->prefix . 'reviews';
$query = "SELECT * FROM $table_name";
$datas = $wpdb->get_results($query);

$grouped_annotations = [];
$count_non_resolu = 0;
$count_resolu = 0;

if( isset($view) ){
    $view = $view;
} 
elseif( isset($_GET['view']) && $_GET['view'] === 'resolved' ){
    $view = 'resolved';
}
else {
    $view = 'active';
}

foreach ($datas as $annotation) {
    $grouped_annotations[$annotation->page_id][] = $annotation;

    if ($annotation->statut === 'non résolu') {
        $count_non_resolu++;
    } elseif ($annotation->statut === 'résolu') {
        $count_resolu++;
    }
}

$interface_color = get_option('wp_annotation_color', 'blue');

?>
<!-- <div class="wp-annotations--dashboard__devices laptop">
    <button class="device laptop">
        <img src="<?= WP_ANNOTATION_URL . 'assets/images/icons/laptop.svg' ?>" alt="Laptop">
    </button>
    <button class="device tablet">
        <img src="<?= WP_ANNOTATION_URL . 'assets/images/icons/tablet.svg' ?>" alt="Tablet">
    </button>
    <button class="device mobile">
        <img src="<?= WP_ANNOTATION_URL . 'assets/images/icons/mobile.svg' ?>" alt="Mobile">
    </button>
</div> -->
<div class="wp-annotations--dashboard__comments<?= $view === 'active' ? ' active' : '' ?>">
    <button class="comments-actives">Actifs (<?= $count_non_resolu ?>)</button>
    <button class="comments-resolved">Résolus (<?= $count_resolu ?>)</button>
</div>

<div class="wp-annotations--dashboard__comments-list active-comments<?= $view === 'active' ? '' : ' display-none' ?>">
    <?php if( $count_non_resolu > 0 ): ?>
        <?php
        foreach ($grouped_annotations as $page_id => $annotations) :
            $page = get_post($page_id);
            $page_title = $page->post_title;
            $page_url = get_permalink($page_id);
            $page_slug = wp_make_link_relative(get_permalink($page_id));

            $count_active = 0;
            $has_active = false;
            foreach ($annotations as $annotation) {
                if ($annotation->statut === 'non résolu') {
                    $has_active = true;
                    $count_active++;
                }
            }

            if ($has_active):
        ?>
        <div class="comment-page-item">
            <div class="accordeon-header">
                <h5><?= $page_slug ?></h5>
                <span>(<?= $count_active ?>)</span>
                <a href="<?= $page_url ?>?review-mode=1" title="Voir la page">
                    <svg width="800px" height="800px" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M6 5.914l2.06-2.06v-.708L5.915 1l-.707.707.043.043.25.25 1 1h-3a2.5 2.5 0 0 0 0 5H4V7h-.5a1.5 1.5 0 1 1 0-3h3L5.207 5.293 5.914 6 6 5.914zM11 2H8.328l-1-1H12l.71.29 3 3L16 5v9l-1 1H6l-1-1V6.5l1 .847V14h9V6h-4V2zm1 0v3h3l-3-3z"/>
                    </svg>
                </a>
                <button class="toggle-accordeon">
                    <svg width="800px" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M4.46938 9.39966C4.76227 9.10677 5.23715 9.10677 5.53004 9.39966L11.894 15.7636C11.9916 15.8613 12.1499 15.8613 12.2476 15.7636L18.6115 9.39966C18.9044 9.10677 19.3793 9.10677 19.6722 9.39966C19.9651 9.69256 19.9651 10.1674 19.6722 10.4603L13.3082 16.8243C12.6248 17.5077 11.5168 17.5077 10.8333 16.8243L4.46938 10.4603C4.17649 10.1674 4.17649 9.69256 4.46938 9.39966Z"/>
                    </svg>
                </button>
            </div>
            <div class="accordeon-content">
                <?php foreach ($annotations as $annotation) : if( $annotation->statut === 'non résolu' ) : ?>
                    <div class="comment-item" data-comment-id="<?= $annotation->id ?>" data-screen-url="<?= $annotation->screenshot_url ?>">
                        <div class="comment-item__header">
                            <div class="dot">
                                <?= $annotation->id ?>
                            </div>
                            <h5><?= get_userdata($annotation->user_id)->display_name ?></h5>
                            <span><?= date('d.m.Y', strtotime($annotation->timestamp)) ?></span>
                            <div class="device">
                                <?php if( $annotation->device === 'laptop' ) : ?>
                                    <svg width="800px" title="laptop" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M10 18H14M7.20003 3H16.8C17.9201 3 18.4802 3 18.908 3.21799C19.2843 3.40973 19.5903 3.71569 19.782 4.09202C20 4.51984 20 5.0799 20 6.2V11.8C20 12.9201 20 13.4802 19.782 13.908C19.5903 14.2843 19.2843 14.5903 18.908 14.782C18.4802 15 17.9201 15 16.8 15H7.20003C6.07992 15 5.51987 15 5.09205 14.782C4.71572 14.5903 4.40976 14.2843 4.21801 13.908C4.00003 13.4802 4.00003 12.9201 4.00003 11.8V6.2C4.00003 5.0799 4.00003 4.51984 4.21801 4.09202C4.40976 3.71569 4.71572 3.40973 5.09205 3.21799C5.51987 3 6.07992 3 7.20003 3ZM4.58888 21H19.4112C20.2684 21 20.697 21 20.9551 20.8195C21.1805 20.6618 21.3311 20.4183 21.3713 20.1462C21.4173 19.8345 21.2256 19.4512 20.8423 18.6845L20.3267 17.6534C19.8451 16.6902 19.6043 16.2086 19.2451 15.8567C18.9274 15.5456 18.5445 15.309 18.1241 15.164C17.6488 15 17.1103 15 16.0335 15H7.96659C6.88972 15 6.35128 15 5.87592 15.164C5.45554 15.309 5.07266 15.5456 4.75497 15.8567C4.39573 16.2086 4.15493 16.6902 3.67334 17.6534L3.1578 18.6845C2.77444 19.4512 2.58276 19.8345 2.6288 20.1462C2.669 20.4183 2.81952 20.6618 3.04492 20.8195C3.30306 21 3.73166 21 4.58888 21Z" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                <?php elseif( $annotation->device === 'tablet' ) : ?>
                                    <svg width="800px" title="tablet" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9 3V4.4C9 4.96005 9 5.24008 9.10899 5.45399C9.20487 5.64215 9.35785 5.79513 9.54601 5.89101C9.75992 6 10.0399 6 10.6 6H13.4C13.9601 6 14.2401 6 14.454 5.89101C14.6422 5.79513 14.7951 5.64215 14.891 5.45399C15 5.24008 15 4.96005 15 4.4V3M8.2 21H15.8C16.9201 21 17.4802 21 17.908 20.782C18.2843 20.5903 18.5903 20.2843 18.782 19.908C19 19.4802 19 18.9201 19 17.8V6.2C19 5.0799 19 4.51984 18.782 4.09202C18.5903 3.71569 18.2843 3.40973 17.908 3.21799C17.4802 3 16.9201 3 15.8 3H8.2C7.0799 3 6.51984 3 6.09202 3.21799C5.71569 3.40973 5.40973 3.71569 5.21799 4.09202C5 4.51984 5 5.07989 5 6.2V17.8C5 18.9201 5 19.4802 5.21799 19.908C5.40973 20.2843 5.71569 20.5903 6.09202 20.782C6.51984 21 7.07989 21 8.2 21Z" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                <?php elseif( $annotation->device === 'mobile' ) : ?>
                                    <svg fill="#000000" title="mobile" width="800px" height="800px" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" class="fill">
                                        <path d="M12.25 0h-8.5A1.25 1.25 0 0 0 2.5 1.25v13.5A1.25 1.25 0 0 0 3.75 16h8.5a1.25 1.25 0 0 0 1.25-1.25V1.25A1.25 1.25 0 0 0 12.25 0zm0 14.75h-8.5V1.25h8.5z"/>
                                        <ellipse cx="8" cy="12.75" rx=".8" ry=".75"/>
                                    </svg>
                                <?php endif; ?>
                            </div>
                            <div class="buttons">
                                <button class="resolve false" title="Résoudre">
                                    <svg width="800px" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="active">
                                        <path d="M4 12.6111L8.92308 17.5L20 6.5" stroke="<?= WP_ANNOTATION_COLORS[$interface_color]['sombre'] ?>" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <svg width="800px" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="resolved">
                                        <path d="M4 12.6111L8.92308 17.5L20 6.5" stroke="#FFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                                <button class="edit" title="Éditer">
                                    <svg width="800px" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M14.7566 2.62145C16.5852 0.792851 19.55 0.792851 21.3786 2.62145C23.2072 4.45005 23.2072 7.41479 21.3786 9.24339L11.8933 18.7287C11.3514 19.2706 11.0323 19.5897 10.6774 19.8665C10.2592 20.1927 9.80655 20.4725 9.32766 20.7007C8.92136 20.8943 8.49334 21.037 7.76623 21.2793L4.43511 22.3897L3.63303 22.6571C2.98247 22.8739 2.26522 22.7046 1.78032 22.2197C1.29542 21.7348 1.1261 21.0175 1.34296 20.367L2.72068 16.2338C2.96303 15.5067 3.10568 15.0787 3.29932 14.6724C3.52755 14.1935 3.80727 13.7409 4.13354 13.3226C4.41035 12.9677 4.72939 12.6487 5.27137 12.1067L14.7566 2.62145ZM4.40051 20.8201L7.24203 19.8729C8.03314 19.6092 8.36927 19.4958 8.68233 19.3466C9.06287 19.1653 9.42252 18.943 9.75492 18.6837C10.0284 18.4704 10.2801 18.2205 10.8698 17.6308L18.4393 10.0614C17.6506 9.78321 16.6346 9.26763 15.6835 8.31651C14.7324 7.36538 14.2168 6.34939 13.9387 5.56075L6.36917 13.1302C5.77951 13.7199 5.52959 13.9716 5.3163 14.2451C5.05704 14.5775 4.83476 14.9371 4.65341 15.3177C4.50421 15.6307 4.3908 15.9669 4.12709 16.758L3.17992 19.5995L4.40051 20.8201ZM15.1554 4.34404C15.1896 4.519 15.2474 4.75684 15.3438 5.03487C15.561 5.66083 15.9712 6.48288 16.7442 7.25585C17.5171 8.02881 18.3392 8.43903 18.9651 8.6562C19.2432 8.75266 19.481 8.81046 19.656 8.84466L20.3179 8.18272C21.5607 6.93991 21.5607 4.92492 20.3179 3.68211C19.0751 2.4393 17.0601 2.4393 15.8173 3.68211L15.1554 4.34404Z" fill="<?= WP_ANNOTATION_COLORS[$interface_color]['sombre'] ?>"/>
                                    </svg>
                                </button>
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
                        </div>

                        <div class="comment-item__content">
                            <p><?= $annotation->commentaire ?></p>
                        </div>

                        <form class="comment-item__content-form">
                            <textarea><?= $annotation->commentaire ?></textarea>
                            <div class="comment-item__content-form--btns">
                                <button class="cancel">Annuler</button>
                                <button type="submit" class="submit">Enregistrer</button>
                            </div>
                        </form>

                        <div class="comment-item__screenshot">
                            <div class="comment-item__screenshot__wrapper">
                                <div class="expend">
                                    <img src="<?= WP_ANNOTATION_URL . 'assets/images/icons/expend.svg' ?>" alt="" class="">
                                </div>
                                <img src="<?= WP_ANNOTATION_URL . 'assets/images/screenshots/' . $annotation->screenshot_url ?>" alt="" class="src-img">
                            </div>
                        </div>
                    </div>
                <?php endif; endforeach; ?>
            </div>
        </div>
        <?php endif; endforeach; ?>
    <?php else: ?>
        <div class="no-comment">
            <p>Aucun commentaire.</p>
        </div>
    <?php endif; ?>
</div>

<div class="wp-annotations--dashboard__comments-list resolved-comments<?= $view === 'active' ? ' display-none' : '' ?>">
    <?php if( $count_resolu > 0 ): ?>
        <?php
        foreach ($grouped_annotations as $page_id => $annotations) :
            $page = get_post($page_id);
            $page_title = $page->post_title;
            $page_url = get_permalink($page_id);
            $page_slug = wp_make_link_relative(get_permalink($page_id));

            $count_resolved = 0;
            $has_resolved = false;
            foreach ($annotations as $annotation) {
                if ($annotation->statut === 'résolu') {
                    $has_resolved = true;
                    $count_resolved++;
                }
            }

            if ($has_resolved):
        ?>
        <div class="comment-page-item">
            <div class="accordeon-header">
                <h5><?= $page_slug ?></h5>
                <span>(<?= $count_resolved ?>)</span>
                <a href="<?= $page_url ?>?review-mode=1&view=resolved" title="Voir la page">
                    <svg width="800px" height="800px" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M6 5.914l2.06-2.06v-.708L5.915 1l-.707.707.043.043.25.25 1 1h-3a2.5 2.5 0 0 0 0 5H4V7h-.5a1.5 1.5 0 1 1 0-3h3L5.207 5.293 5.914 6 6 5.914zM11 2H8.328l-1-1H12l.71.29 3 3L16 5v9l-1 1H6l-1-1V6.5l1 .847V14h9V6h-4V2zm1 0v3h3l-3-3z"/>
                    </svg>
                </a>
                <button class="toggle-accordeon">
                    <svg width="800px" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M4.46938 9.39966C4.76227 9.10677 5.23715 9.10677 5.53004 9.39966L11.894 15.7636C11.9916 15.8613 12.1499 15.8613 12.2476 15.7636L18.6115 9.39966C18.9044 9.10677 19.3793 9.10677 19.6722 9.39966C19.9651 9.69256 19.9651 10.1674 19.6722 10.4603L13.3082 16.8243C12.6248 17.5077 11.5168 17.5077 10.8333 16.8243L4.46938 10.4603C4.17649 10.1674 4.17649 9.69256 4.46938 9.39966Z"/>
                    </svg>
                </button>
            </div>
            <div class="accordeon-content">
                <?php foreach ($annotations as $annotation) : if( $annotation->statut === 'résolu' ) : ?>
                    <div class="comment-item" data-comment-id="<?= $annotation->id ?>">
                        <div class="comment-item__header">
                            <div class="dot">
                                <?= $annotation->id ?>
                            </div>
                            <h5><?= get_userdata($annotation->user_id)->display_name ?></h5>
                            <span><?= date('d.m.Y', strtotime($annotation->timestamp)) ?></span>
                            <div class="buttons">
                                <button class="resolve true" title="Résoudre">
                                    <svg width="800px" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="active">
                                        <path d="M4 12.6111L8.92308 17.5L20 6.5" stroke="<?= WP_ANNOTATION_COLORS[$interface_color]['sombre'] ?>" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <svg width="800px" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="resolved">
                                        <path d="M4 12.6111L8.92308 17.5L20 6.5" stroke="#FFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
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
                        </div>
                        <div class="comment-item__content">
                            <p><?= $annotation->commentaire ?></p>
                        </div>

                        <div class="comment-item__screenshot">
                            <div class="comment-item__screenshot__wrapper">
                                <div class="expend">
                                    <img src="<?= WP_ANNOTATION_URL . 'assets/images/icons/expend.svg' ?>" alt="" class="">
                                </div>
                                <img src="<?= WP_ANNOTATION_URL . 'assets/images/screenshots/' . $annotation->screenshot_url ?>" alt="" class="src-img">
                            </div>
                        </div>
                    </div>
                <?php endif; endforeach; ?>
            </div>
        </div>
        <?php endif; endforeach; ?>
    <?php else: ?>
        <div class="no-comment">
            <p>Aucun commentaire.</p>
        </div>
    <?php endif; ?>
</div>

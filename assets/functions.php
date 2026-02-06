<?php

//  ######  ######## ##    ## ########     ######## ##     ##    ###    #### ##       
// ##    ## ##       ###   ## ##     ##    ##       ###   ###   ## ##    ##  ##       
// ##       ##       ####  ## ##     ##    ##       #### ####  ##   ##   ##  ##       
//  ######  ######   ## ## ## ##     ##    ######   ## ### ## ##     ##  ##  ##       
//       ## ##       ##  #### ##     ##    ##       ##     ## #########  ##  ##       
// ##    ## ##       ##   ### ##     ##    ##       ##     ## ##     ##  ##  ##       
//  ######  ######## ##    ## ########     ######## ##     ## ##     ## #### ######## 

function sendNotificationEmail($datas, $comment, $notifications, $highLvl = false) {
    $emails = getUsersEmails($datas, $highLvl ? $comment : $comment[0], $notifications);
    $current_user_name = get_userdata(get_current_user_id())->display_name;
    $smtp_name = get_option('wp_annotation_smtp_from_name', '');
    $smtp_email = get_option('wp_annotation_smtp_from_email', '');

    $headers = array(
        'Content-Type: text/html; charset=UTF-8'
    );
    
    if (!empty($smtp_email) && !empty($smtp_name)) {
        $headers[] = 'From: ' . $smtp_name . ' <' . $smtp_email . '>';
    }

    foreach($emails as $email){
        if(!empty($email[0])){
            if( $email[1] ){
                $subject = 'Mention de commentaire - ' . get_bloginfo('name'); 
            }
            else{
                $subject = 'Réponse à votre commentaire - ' . get_bloginfo('name'); 
            }
    
            $message = createNotificationsMessage( $datas, $email[1], $current_user_name, $comment, $highLvl );
            
            $sent = wp_mail($email[0], $subject, $message, $headers);
            
            if($sent){
                error_log('✅ Email envoyé avec succès à ' . $email[0]);
            } else {
                error_log('❌ Erreur d\'envoi de l\'email à ' . $email[0]);
            }
        }
    }
}

// Check if user is allowed to see annotations
function is_user_allowed_for_annotations() {
    $current_user = wp_get_current_user();

    $allowed_users = get_option('wp_annotation_users', []);

    return in_array($current_user->ID, (array) $allowed_users);
}

function get_wp_annotations_users_by_name() {
    $users = get_option('wp_annotation_users', []);
    $users_array = array();

    foreach ($users as $user) {
        $userdata = get_userdata($user);
        $users_array[$userdata->ID] = $userdata->display_name;
    }

    return $users_array;
}

// Email message
function createNotificationsMessage( $datas, $notif, $current_user_name, $comment, $highLvl = false ) {
    $interface_color = get_option('wp_annotation_color', 'blue');

    $message = '<html><body>';
    $message .= '<table style="width: 100%; font-family: Arial, sans-serif; line-height: 1.6;">';

    $message .= '<tr><th style="background-color: ' . WP_ANNOTATION_COLORS[$interface_color]['sombre'] . '; padding: 16px 24px; font-size: 20px; color: #FFF; text-align: center;">';
    $message .= '<p style="margin: 0;">WP Annotations</p>';
    $message .= '</th></tr>';
    $message .= '<tr style="background-color: #f2f2f2;"><td style="padding: 0 16px">';
    $message .= '<p style="font-size: 16px; color: ' . WP_ANNOTATION_COLORS[$interface_color]['sombre'] . ';">Bonjour,</p>';
    if($notif || $highLvl){
        $message .= '<p style="font-size: 16px; color: ' . WP_ANNOTATION_COLORS[$interface_color]['sombre'] . ';"><strong>' . $current_user_name . '</strong> vous a mentionné sous un commentaire sur le site <strong>' . get_bloginfo('name') . '</strong> :</p>';
    }
    else{
        $message .= '<p style="font-size: 16px; color: ' . WP_ANNOTATION_COLORS[$interface_color]['sombre'] . ';"><strong>' . $current_user_name . '</strong> a ajouté une réponse sous un de vos commentaires sur le site <strong>' . get_bloginfo('name') . '</strong> :</p>';
    }
    $message .= '<blockquote style="font-size: 16px; background-color: #f7f7f7; color: ' . WP_ANNOTATION_COLORS[$interface_color]['sombre'] . '; border-left: 4px solid ' . WP_ANNOTATION_COLORS[$interface_color]['sombre'] . '; padding: 6px 10px; margin-left: 0; font-style: italic;">';
    $message .= formatNotificationsComment($highLvl ? $comment : $comment[0]);

    if($highLvl){
        $message .= '<img src="' . WP_ANNOTATION_URL . 'assets/images/screenshots/' . $datas['screenshot_url'] . '" alt="Image" style="width: 100%; max-width: 600px; height: auto; margin: 0 auto;">';
    }
    elseif(!empty($comment[1])){
        $message .= '<img src="' . WP_ANNOTATION_URL . 'assets/images/replies/' . $comment[1] . '" alt="Image" style="width: 100%; max-width: 600px; height: auto; margin: 0 auto;">';
    }
    $message .= '</blockquote>';

    if ( !$highLvl ) {
        $message .= '<p style="font-size: 16px; color: ' . WP_ANNOTATION_COLORS[$interface_color]['sombre'] . ';">Voici le commentaire original :</p>';
        $message .= '<blockquote style="font-size: 16px; background-color: #f7f7f7; color: ' . WP_ANNOTATION_COLORS[$interface_color]['sombre'] . '; border-left: 4px solid ' . WP_ANNOTATION_COLORS[$interface_color]['sombre'] . '; padding: 6px 10px; margin-left: 0; font-style: italic;">' . nl2br(esc_html($datas['commentaire'])) . '</blockquote>';
    }
    $message .= '<p style="font-size: 16px; color: ' . WP_ANNOTATION_COLORS[$interface_color]['sombre'] . ';">Vous pouvez consulter le commentaire en vous connectant à votre <a href="' . get_admin_url() . '" style="color: #0075A2;">espace administrateur</a>.</p>';
    $message .= '<br>';
    $message .= '<p style="font-size: 16px; color: ' . WP_ANNOTATION_COLORS[$interface_color]['sombre'] . ';">Bonne journée !</p>';
    $message .= '</td></tr>';
    $message .= '</table>';
    $message .= '</body></html>'; 

    return $message;
}

// Format notifications comment
function formatNotificationsComment($comment) {
    $comment = esc_html($comment);

    $paragraphs = explode("\n", trim($comment));
    $comment = '<p>' . implode('</p><p>', array_filter($paragraphs)) . '</p>';

    $pattern = '/@([a-zA-Z0-9_]+)/';
    $formattedComment = preg_replace_callback($pattern, function($matches) {
        return '<strong class="mention">@' . esc_html($matches[1]) . '</strong>';
    }, $comment);

    return $formattedComment;
}

// Check if SMTP is filled
function checkSMTPSettings(){
    $smtp_mail = get_option('wp_annotation_smtp_mail', '');
    $smtp_user = get_option('wp_annotation_smtp_user', '');
    $smtp_password = get_option('wp_annotation_smtp_password', '');
    $smtp_name = get_option('wp_annotation_smtp_from_name', '');
    $smtp_email = get_option('wp_annotation_smtp_from_email', '');

    if ( empty($smtp_mail) || empty($smtp_user) || empty($smtp_password) || empty($smtp_name) || empty($smtp_email) ) {
        update_option('wp_annotation_smtp_valid', false);
        return;
    }

    update_option('wp_annotation_smtp_valid', true);
}

add_action('init', 'checkSMTPSettings');

// Plugin dehactivation
add_action( 'admin_footer', 'wp_annotations_dehactivation_warning' );

function wp_annotations_dehactivation_warning() {
?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('#deactivate-wp-annotations-plugin').on('click', function(e) {
                if (confirm("Voulez-vous aussi supprimer toutes les données associées à ce plugin ?")) {
                    
                    $.post(ajaxurl, {
                        action: 'flush_reviews',
                        context: 'dehactivate'
                    }, function(response) {
                        if (response.success) {
                            window.location.href = $(e.target).attr('href');
                        } else {
                            return false;
                            alert("Une erreur s'est produite lors de la suppression des données.");
                        }
                    }).fail(function() {
                        return false;
                        alert("Une erreur s'est produite lors de la suppression des données.");
                    });
                }
                else{
                    window.location.href = $(e.target).attr('href');
                }
            });
        });
    </script>
<?php
}

// CHECK USER ROLE
function check_user_role( $user = null ) {
    if( $user === null ) {
        $user = wp_get_current_user();
    }

    if( 
        in_array( WP_ANNOTATION_ROLE, (array) $user->roles ) ||
        in_array( 'administrator', (array) $user->roles )
    ) {
        return true;
    } else {
        return false;
    }
}
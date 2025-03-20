<?php
if (!defined('ABSPATH')) {
    exit;
}

// Check if user is allowed to see annotations
function is_user_allowed_for_annotations() {
    $current_user = wp_get_current_user();

    $allowed_users = get_option('wp_annotation_users', []);

    return in_array($current_user->ID, (array) $allowed_users);
}

// Get all comment replies
function getAllReplies($comment_id) {
    global $wpdb;
    $table_name_replies = $wpdb->prefix . 'reviews_replies';

    $sql = "SELECT * FROM $table_name_replies WHERE comment_id = $comment_id ORDER BY timestamp ASC";

    return $wpdb->get_results($sql);
}

// Extract users Emails
function extractUsersEmails($datas){
    $users_array_raw = array();
    $replies = getAllReplies($datas['id']);

    $users_array_raw[] = $datas['user_id'];

    if(!empty($replies)){
        foreach($replies as $reply){
            $users_array_raw[] = $reply->user_id;
        }
    }

    $users_array = array_unique($users_array_raw);
    $users_array = array_values($users_array);

    foreach($users_array as $user_id){
        if($user_id != get_current_user_id()){
            $user = get_userdata($user_id);
            $users_emails[] = $user->user_email;
        }
    }

    return $users_emails;
}

// Send notification email to user
function sendNotificationEmail($datas, $comment){
    $emails = extractUsersEmails($datas);
    $current_user_name = get_userdata(get_current_user_id())->display_name;
    $interface_color = get_option('wp_annotation_color', 'blue');
    
    $subject = 'Réponse à votre commentaire - ' . get_bloginfo('name');

    $message = '<html><body>';
    $message .= '<table style="width: 100%; font-family: Arial, sans-serif; line-height: 1.6;">';

    $message .= '<tr><th style="background-color: ' . WP_ANNOTATION_COLORS[$interface_color]['sombre'] . '; padding: 16px 24px; font-size: 20px; color: #FFF; text-align: center;">';
    $message .= '<p style="margin: 0;">WP Annotations</p>';
    $message .= '</th></tr>';
    $message .= '<tr style="background-color: #f2f2f2;"><td style="padding: 0 16px">';
    $message .= '<p style="font-size: 16px; color: ' . WP_ANNOTATION_COLORS[$interface_color]['sombre'] . ';">Bonjour,</p>';
    $message .= '<p style="font-size: 16px; color: ' . WP_ANNOTATION_COLORS[$interface_color]['sombre'] . ';">' . $current_user_name . ' a ajouté une réponse sous un de vos commentaires sur le site <strong>' . get_bloginfo('name') . '</strong> :</p>';
    $message .= '<blockquote style="font-size: 16px; background-color: #f7f7f7; color: ' . WP_ANNOTATION_COLORS[$interface_color]['sombre'] . '; border-left: 4px solid ' . WP_ANNOTATION_COLORS[$interface_color]['sombre'] . '; padding: 6px 10px; margin-left: 0; font-style: italic;">' . nl2br(esc_html($comment)) . '</blockquote>';
    $message .= '<p style="font-size: 16px; color: ' . WP_ANNOTATION_COLORS[$interface_color]['sombre'] . ';">Voici le commentaire original :</p>';
    $message .= '<blockquote style="font-size: 16px; background-color: #f7f7f7; color: ' . WP_ANNOTATION_COLORS[$interface_color]['sombre'] . '; border-left: 4px solid ' . WP_ANNOTATION_COLORS[$interface_color]['sombre'] . '; padding: 6px 10px; margin-left: 0; font-style: italic;">' . nl2br(esc_html($datas['commentaire'])) . '</blockquote>';
    $message .= '<p style="font-size: 16px; color: ' . WP_ANNOTATION_COLORS[$interface_color]['sombre'] . ';">Vous pouvez consulter la réponse en vous connectant à votre <a href="' . get_admin_url() . '" style="color: #0075A2;">espace administrateur</a>.</p>';
    $message .= '<br>';
    $message .= '<p style="font-size: 16px; color: ' . WP_ANNOTATION_COLORS[$interface_color]['sombre'] . ';">Bonne journée !</p>';
    $message .= '</td></tr>';
    $message .= '</table>';
    $message .= '</body></html>';  

    $site_url = get_site_url();

    $headers = "From: no-reply@" . parse_url($site_url, PHP_URL_HOST) . "\r\n";
    $headers .= "Reply-To: no-reply@" . parse_url($site_url, PHP_URL_HOST) . "\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    foreach($emails as $email){
        // mail($email, $subject, $message, $headers);
        if(wp_mail($email, $subject, $message, $headers)) {
            error_log( "E-mail envoyé avec succès !");
        } else {
            error_log( "Échec de l'envoi d'e-mail.");
        }
    }
}
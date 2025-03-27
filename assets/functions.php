<?php
if (!defined('ABSPATH')) {
    exit;
}

require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';
require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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

// Get all comment replies
function getAllReplies($comment_id) {
    global $wpdb;
    $table_name_replies = $wpdb->prefix . 'reviews_replies';

    $sql = "SELECT * FROM $table_name_replies WHERE comment_id = $comment_id ORDER BY timestamp ASC";

    return $wpdb->get_results($sql);
}

// Extract users Emails
function extractUsersEmails($datas, $comment, $notifications){
    $users_array = array();
    $replies = getAllReplies($datas['id']);

    $users_array[$datas['user_id']] = false;

    if(!empty($replies)){
        foreach($replies as $reply){
            $users_array[$reply->user_id] = false;
        }
    }

    if(!empty($notifications)){
        foreach($notifications as $not_id){
            $username = get_userdata($not_id)->display_name;
            $pattern = '/@' . preg_quote($username, '/') . '/';

            if(preg_match($pattern, $comment)){
                $users_array[$not_id] = true;
            }
        }
    }

    foreach($users_array as $id => $notified){
        if($id != get_current_user_id()){
            $user = get_userdata($id);
            $users_emails[] = [$user->user_email, $notified];
        }
    }

    return $users_emails;
}

// Send notification email to user
function sendNotificationEmail($datas, $comment, $notifications){
    $emails = extractUsersEmails($datas, $comment, $notifications);
    $current_user_name = get_userdata(get_current_user_id())->display_name;
    $smtp = get_option('wp_annotation_smtp_valid', false);
    $mail = new PHPMailer(true);
    $site_url = get_site_url();

    if(!$smtp){
        error_log('❌ SMTP non configuré.');
        return;
    }

    $smtp_mail = get_option('wp_annotation_smtp_mail', '');
    $smtp_user = get_option('wp_annotation_smtp_user', '');
    $smtp_password = get_option('wp_annotation_smtp_password', '');
    $smtp_name = get_option('wp_annotation_smtp_from_name', '');
    $smtp_email = get_option('wp_annotation_smtp_from_email', '');

    try {
        $mail->isSMTP();
        $mail->Host       = $smtp_mail;
        $mail->SMTPAuth   = true;
        $mail->Username   = $smtp_user;
        $mail->Password   = $smtp_password;
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
        $mail->isHTML(true);

        $mail->setFrom($smtp_email, $smtp_name);

        foreach($emails as $email){
            $mail->addAddress($email[0]);

            if( $email[1] ){
                $mail->Subject = 'Mention de commentaire - ' . get_bloginfo('name'); 
            }
            else{
                $mail->Subject = 'Réponse à votre commentaire - ' . get_bloginfo('name'); 
            }
    
            $mail->Body = createNotificationsMessage( $datas, $email[1], $current_user_name, $comment );
    
            $mail->send();
            error_log('✅ Email envoyé avec succès !');
        }
    } catch (Exception $e) {
        error_log('❌ Erreur d\'envoi : ' . $mail->ErrorInfo);
    }
}

// Email message
function createNotificationsMessage( $datas, $notif, $current_user_name, $comment ){
    $interface_color = get_option('wp_annotation_color', 'blue');

    $message = '<html><body>';
    $message .= '<table style="width: 100%; font-family: Arial, sans-serif; line-height: 1.6;">';

    $message .= '<tr><th style="background-color: ' . WP_ANNOTATION_COLORS[$interface_color]['sombre'] . '; padding: 16px 24px; font-size: 20px; color: #FFF; text-align: center;">';
    $message .= '<p style="margin: 0;">WP Annotations</p>';
    $message .= '</th></tr>';
    $message .= '<tr style="background-color: #f2f2f2;"><td style="padding: 0 16px">';
    $message .= '<p style="font-size: 16px; color: ' . WP_ANNOTATION_COLORS[$interface_color]['sombre'] . ';">Bonjour,</p>';
    if($notif){
        $message .= '<p style="font-size: 16px; color: ' . WP_ANNOTATION_COLORS[$interface_color]['sombre'] . ';">' . $current_user_name . ' vous a mentionné sous un commentaire sur le site <strong>' . get_bloginfo('name') . '</strong> :</p>';
    }
    else{
        $message .= '<p style="font-size: 16px; color: ' . WP_ANNOTATION_COLORS[$interface_color]['sombre'] . ';">' . $current_user_name . ' a ajouté une réponse sous un de vos commentaires sur le site <strong>' . get_bloginfo('name') . '</strong> :</p>';
    }
    $message .= '<blockquote style="font-size: 16px; background-color: #f7f7f7; color: ' . WP_ANNOTATION_COLORS[$interface_color]['sombre'] . '; border-left: 4px solid ' . WP_ANNOTATION_COLORS[$interface_color]['sombre'] . '; padding: 6px 10px; margin-left: 0; font-style: italic;">' . nl2br(esc_html($comment)) . '</blockquote>';
    $message .= '<p style="font-size: 16px; color: ' . WP_ANNOTATION_COLORS[$interface_color]['sombre'] . ';">Voici le commentaire original :</p>';
    $message .= '<blockquote style="font-size: 16px; background-color: #f7f7f7; color: ' . WP_ANNOTATION_COLORS[$interface_color]['sombre'] . '; border-left: 4px solid ' . WP_ANNOTATION_COLORS[$interface_color]['sombre'] . '; padding: 6px 10px; margin-left: 0; font-style: italic;">' . nl2br(esc_html($datas['commentaire'])) . '</blockquote>';
    $message .= '<p style="font-size: 16px; color: ' . WP_ANNOTATION_COLORS[$interface_color]['sombre'] . ';">Vous pouvez consulter la réponse en vous connectant à votre <a href="' . get_admin_url() . '" style="color: #0075A2;">espace administrateur</a>.</p>';
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
        return '<span class="mention">@' . esc_html($matches[1]) . '</span>';
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
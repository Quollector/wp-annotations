<?php
if ( ! defined( 'ABSPATH' ) ) exit;

//    ###    ########  ########     ##    ## ######## ##      ##       ###    ##    ## ##    ##  #######  ######## 
//   ## ##   ##     ## ##     ##    ###   ## ##       ##  ##  ##      ## ##   ###   ## ###   ## ##     ##    ##    
//  ##   ##  ##     ## ##     ##    ####  ## ##       ##  ##  ##     ##   ##  ####  ## ####  ## ##     ##    ##    
// ##     ## ##     ## ##     ##    ## ## ## ######   ##  ##  ##    ##     ## ## ## ## ## ## ## ##     ##    ##    
// ######### ##     ## ##     ##    ##  #### ##       ##  ##  ##    ######### ##  #### ##  #### ##     ##    ##    
// ##     ## ##     ## ##     ##    ##   ### ##       ##  ##  ##    ##     ## ##   ### ##   ### ##     ##    ##    
// ##     ## ########  ########     ##    ## ########  ###  ###     ##     ## ##    ## ##    ##  #######     ##    

function wp_annotations_submit_comment() {
    check_ajax_referer( 'wp_annotations_nonce', 'nonce' );

    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Vous devez être connecté pour ajouter un commentaire.']);
        return;
    }

    if (isset($_POST['datas']) && is_array($_POST['datas'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'reviews';
        $datas = $_POST['datas'];

        parse_str($datas[0], $form_data);

        $commentaire = isset($form_data['comment']) ? wp_kses_post(stripslashes($form_data['comment'])) : '';
        $client_visible = isset($form_data['client_visible']) ? intval($form_data['client_visible']) : 0;
        $device = isset($datas[1]) ? sanitize_text_field($datas[1]) : '';
        $targetsEmail = isset($form_data['targets_email']) ? array_map('intval', (array) $form_data['targets_email']) : [];

        if (empty(trim($commentaire))) {
            wp_send_json_error(['message' => 'Le commentaire ne peut pas être vide.']);
            return;
        }

        // Gérer le screenshot
        $screenshot_path = '';
        if (!empty($_POST['screenshot'])) {
            $upload_dir = WP_ANNOTATION_PATH . '/assets/images/screenshots/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $unique_id = uniqid(); // ID unique pour le fichier
            $file_name = "screen_{$unique_id}.png";
            $file_path = $upload_dir . $file_name;
            
            $screenshot_data = $_POST['screenshot'];
            $screenshot_data = str_replace('data:image/png;base64,', '', $screenshot_data);
            $screenshot_data = base64_decode($screenshot_data, true);

            // Vérifier la signature PNG (magic bytes)
            if ( $screenshot_data === false || substr($screenshot_data, 0, 8) !== "\x89PNG\r\n\x1a\n" ) {
                wp_send_json_error(['message' => 'Format de capture écran invalide.']);
                return;
            }

            file_put_contents($file_path, $screenshot_data);
            $screenshot_path = $file_name;
        }

        $table_datas = [
            'commentaire' => $commentaire,
            'device' => $device,
            'page_id' => intval($datas[2]),
            'user_id' => get_current_user_id(),
            'timestamp' => current_time('mysql'),
            'statut' => 'non résolu',
            'screenshot_url' => $screenshot_path,
            'client_visible' => $client_visible
        ];

        // Insérer le commentaire dans la DB
        $insert = $wpdb->insert(
            $table_name,
            $table_datas
        );

        if ($insert) { 
            $variables = [
                $device = isset($_POST['device']) ? sanitize_text_field($_POST['device']) : '',
                $view = sanitize_text_field($_POST['view']),
                $viewDevice = sanitize_text_field($_POST['deviceView'])
            ];

            ob_start();            
            extract($variables);
            include WP_ANNOTATION_PATH . 'views/frontend/comments-box.php';
            $comments_content = ob_get_clean();
            
            if ( !empty($targetsEmail) ) {
                sendNotificationEmail( $table_datas, $table_datas['commentaire'], $targetsEmail, true );
            }

            wp_send_json_success([
                'message' => 'Commentaire ajouté avec succès',
                'comments_content' => $comments_content,
                'screenshot' => $screenshot_path
            ]);
        } else {
            wp_send_json_error(['message' => 'Une erreur est survenue lors de l\'ajout du commentaire.']);
        }
    } else {
        wp_send_json_error(['message' => 'Données invalides reçues.']);
    }
}

add_action('wp_ajax_wp_annotations_submit_comment', 'wp_annotations_submit_comment');

// ######## #### ##       ######## ######## ########      ######   #######  ##     ## ##     ## ######## ##    ## ########  ######  
// ##        ##  ##          ##    ##       ##     ##    ##    ## ##     ## ###   ### ###   ### ##       ###   ##    ##    ##    ## 
// ##        ##  ##          ##    ##       ##     ##    ##       ##     ## #### #### #### #### ##       ####  ##    ##    ##       
// ######    ##  ##          ##    ######   ########     ##       ##     ## ## ### ## ## ### ## ######   ## ## ##    ##     ######  
// ##        ##  ##          ##    ##       ##   ##      ##       ##     ## ##     ## ##     ## ##       ##  ####    ##          ## 
// ##        ##  ##          ##    ##       ##    ##     ##    ## ##     ## ##     ## ##     ## ##       ##   ###    ##    ##    ## 
// ##       #### ########    ##    ######## ##     ##     ######   #######  ##     ## ##     ## ######## ##    ##    ##     ######  

function wp_annotations_filter_comments() {
    check_ajax_referer( 'wp_annotations_nonce', 'nonce' );

    if ( ! is_user_logged_in() ) {
        wp_send_json_error(['message' => 'Permission refusée.']);
        return;
    }

    $variables = [
        $view = sanitize_text_field($_POST['view']),
        $viewDevice = sanitize_text_field($_POST['deviceView'])
    ];

    ob_start();            
    extract($variables);
    include WP_ANNOTATION_PATH . 'views/frontend/comments-box.php';
    $comments_content = ob_get_clean(); 

    wp_send_json_success([
        'comments_content' => $comments_content
    ]);
}

add_action('wp_ajax_filter_wp_annotations_comments', 'wp_annotations_filter_comments');

// ##     ## ########  ########     ###    ######## ########     ######  ########    ###    ######## ##     ##  ######  
// ##     ## ##     ## ##     ##   ## ##      ##    ##          ##    ##    ##      ## ##      ##    ##     ## ##    ## 
// ##     ## ##     ## ##     ##  ##   ##     ##    ##          ##          ##     ##   ##     ##    ##     ## ##       
// ##     ## ########  ##     ## ##     ##    ##    ######       ######     ##    ##     ##    ##    ##     ##  ######  
// ##     ## ##        ##     ## #########    ##    ##                ##    ##    #########    ##    ##     ##       ## 
// ##     ## ##        ##     ## ##     ##    ##    ##          ##    ##    ##    ##     ##    ##    ##     ## ##    ## 
//  #######  ##        ########  ##     ##    ##    ########     ######     ##    ##     ##    ##     #######   ######  

function wp_annotations_update_status() {
    check_ajax_referer( 'wp_annotations_nonce', 'nonce' );

    if ( ! is_user_logged_in() || ! check_user_role() ) {
        wp_send_json_error(['message' => 'Permission refusée.']);
        return;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'reviews';

    $id = intval($_POST['id']);

    $variables = [
        $view = sanitize_text_field($_POST['view']),
        $viewDevice = sanitize_text_field($_POST['deviceView'])
    ];

    $currentStatus = $wpdb->get_var( $wpdb->prepare( "SELECT statut FROM $table_name WHERE id = %d", $id ) );

    $newStatus = ( $currentStatus === 'résolu' ) ? 'non résolu' : 'résolu';

    $update = $wpdb->update(
        $table_name,
        [ 'statut' => $newStatus ],
        [ 'id' => $id ],
        [ '%s' ],
        [ '%d' ]
    );

    ob_start();            
    extract($variables);
    include WP_ANNOTATION_PATH . 'views/frontend/comments-box.php';
    $comments_content = ob_get_clean(); 

    wp_send_json_success([
        'message' => ($newStatus === 'résolu') ? 'Statut du commentaire #' . $id . ' passé à "Résolu"' : 'Statut du commentaire #' . $id . ' passé à "Actif"',
        'comments_content' => $comments_content
    ]);
}

add_action('wp_ajax_wp_annotations_update_status', 'wp_annotations_update_status');

// ######## ########  #### ########     ######   #######  ##     ## ##     ## ######## ##    ## ######## 
// ##       ##     ##  ##     ##       ##    ## ##     ## ###   ### ###   ### ##       ###   ##    ##    
// ##       ##     ##  ##     ##       ##       ##     ## #### #### #### #### ##       ####  ##    ##    
// ######   ##     ##  ##     ##       ##       ##     ## ## ### ## ## ### ## ######   ## ## ##    ##    
// ##       ##     ##  ##     ##       ##       ##     ## ##     ## ##     ## ##       ##  ####    ##    
// ##       ##     ##  ##     ##       ##    ## ##     ## ##     ## ##     ## ##       ##   ###    ##    
// ######## ########  ####    ##        ######   #######  ##     ## ##     ## ######## ##    ##    ##    

function wp_annotations_edit_comment() {
    check_ajax_referer( 'wp_annotations_nonce', 'nonce' );

    if ( ! is_user_logged_in() || ! check_user_role() ) {
        wp_send_json_error(['message' => 'Permission refusée.']);
        return;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'reviews';

    $id = intval($_POST['id']);
    $comment = wp_kses_post(stripslashes($_POST['comment']));

    $variables = [
        $view = sanitize_text_field($_POST['view']),
        $viewDevice = sanitize_text_field($_POST['deviceView'])
    ];

    $update = $wpdb->update(
        $table_name,
        [ 'commentaire' => $comment ],
        [ 'id' => $id ],
        [ '%s' ],
        [ '%d' ]
    );

    ob_start();            
    extract($variables);
    include WP_ANNOTATION_PATH . 'views/frontend/comments-box.php';
    $comments_content = ob_get_clean(); 

    wp_send_json_success([
        'message' => 'Commentaire #' . $id . ' modifié',
        'comments_content' => $comments_content
    ]);
}

add_action('wp_ajax_wp_annotations_edit_comment', 'wp_annotations_edit_comment');

// ########  ######## ##       ######## ######## ########     ######   #######  ##     ## ##     ## ######## ##    ## ######## 
// ##     ## ##       ##       ##          ##    ##          ##    ## ##     ## ###   ### ###   ### ##       ###   ##    ##    
// ##     ## ##       ##       ##          ##    ##          ##       ##     ## #### #### #### #### ##       ####  ##    ##    
// ##     ## ######   ##       ######      ##    ######      ##       ##     ## ## ### ## ## ### ## ######   ## ## ##    ##    
// ##     ## ##       ##       ##          ##    ##          ##       ##     ## ##     ## ##     ## ##       ##  ####    ##    
// ##     ## ##       ##       ##          ##    ##          ##    ## ##     ## ##     ## ##     ## ##       ##   ###    ##    
// ########  ######## ######## ########    ##    ########     ######   #######  ##     ## ##     ## ######## ##    ##    ##    

function wp_annotations_delete_comment() {
    check_ajax_referer( 'wp_annotations_nonce', 'nonce' );

    if ( ! is_user_logged_in() || ! check_user_role() ) {
        wp_send_json_error(['message' => 'Permission refusée.']);
        return;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'reviews';
    $table_replies = $wpdb->prefix . 'reviews_replies';
    $id = intval($_POST['id']);
    $delete = array();

    $filename = $wpdb->get_row($wpdb->prepare("SELECT screenshot_url FROM $table_name WHERE id = %d", $id), ARRAY_A);
    $screenUrl = $filename['screenshot_url'];

    $file_path = WP_ANNOTATION_PATH . 'assets/images/screenshots/' . $screenUrl;
    if (file_exists($file_path)) {
        unlink($file_path);
    }

    $variables = [
        $view = sanitize_text_field($_POST['view']),
        $viewDevice = sanitize_text_field($_POST['deviceView'])
    ];

    $delete[] = $wpdb->delete(
        $table_name,
        ['id' => $id],
        ['%d']
    );

    $repliesFiles = $wpdb->get_results($wpdb->prepare("SELECT file_path FROM $table_replies WHERE comment_id = %d", $id), ARRAY_A);
    
    if( !empty($repliesFiles) ){
        foreach($repliesFiles as $repliesFile){
            $screenUrl = $repliesFile['file_path'];
            $file_path = WP_ANNOTATION_PATH . 'assets/images/replies/' . $screenUrl;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }

    $delete[] = $wpdb->delete(
        $table_replies,
        ['comment_id' => $id]
    );

    if( $delete[0] !== false && $delete[1] !== false ){
        ob_start();            
        extract($variables);
        include WP_ANNOTATION_PATH . 'views/frontend/comments-box.php';
        $comments_content = ob_get_clean(); 
    
        wp_send_json_success([
            'message' => 'Commentaire #' . $id . ' supprimé',
            'comments_content' => $comments_content
        ]);
    } 
    else {
        wp_send_json_error(['message' => 'Une erreur est survenue lors de la suppression du commentaire.']);
    }
}

add_action('wp_ajax_wp_annotations_delete_comment', 'wp_annotations_delete_comment');

//  #######  ########  ######## ##    ##    ########  ######## ########  ##       #### ########  ######  
// ##     ## ##     ## ##       ###   ##    ##     ## ##       ##     ## ##        ##  ##       ##    ## 
// ##     ## ##     ## ##       ####  ##    ##     ## ##       ##     ## ##        ##  ##       ##       
// ##     ## ########  ######   ## ## ##    ########  ######   ########  ##        ##  ######    ######  
// ##     ## ##        ##       ##  ####    ##   ##   ##       ##        ##        ##  ##             ## 
// ##     ## ##        ##       ##   ###    ##    ##  ##       ##        ##        ##  ##       ##    ## 
//  #######  ##        ######## ##    ##    ##     ## ######## ##        ######## #### ########  ######  

// === First display
function wp_annotations_open_reply() {
    check_ajax_referer( 'wp_annotations_nonce', 'nonce' );

    if ( ! is_user_logged_in() ) {
        wp_send_json_error(['message' => 'Permission refusée.']);
        return;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'reviews';

    $comment_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", intval($_POST['id'])), ARRAY_A);

    if (!$comment_data) {
        wp_send_json_error(['message' => 'Commentaire non trouvé.']);
    }

    ob_start();
    extract($comment_data);
    include WP_ANNOTATION_PATH . 'views/frontend/replies/replies-box.php';
    $reply_content = ob_get_clean();
    
    wp_send_json_success([
        'reply_content' => $reply_content
    ]);
}

add_action('wp_ajax_wp_annotations_open_reply', 'wp_annotations_open_reply');

//  ######  ##     ## ########  ##     ## #### ########    ########  ######## ########  ##       ##    ## 
// ##    ## ##     ## ##     ## ###   ###  ##     ##       ##     ## ##       ##     ## ##        ##  ##  
// ##       ##     ## ##     ## #### ####  ##     ##       ##     ## ##       ##     ## ##         ####   
//  ######  ##     ## ########  ## ### ##  ##     ##       ########  ######   ########  ##          ##    
//       ## ##     ## ##     ## ##     ##  ##     ##       ##   ##   ##       ##        ##          ##    
// ##    ## ##     ## ##     ## ##     ##  ##     ##       ##    ##  ##       ##        ##          ##    
//  ######   #######  ########  ##     ## ####    ##       ##     ## ######## ##        ########    ##    

function wp_annotations_submit_reply() {
    check_ajax_referer( 'wp_annotations_nonce', 'nonce' );

    if ( ! is_user_logged_in() ) {
        wp_send_json_error(['message' => 'Permission refusée.']);
        return;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'reviews_replies';
    $commentID = isset($_POST['comment_id']) ? intval($_POST['comment_id']) : 0;
    $userID = get_current_user_id();
    $commentText = isset($_POST['comment_text']) ? wp_kses_post(stripslashes($_POST['comment_text'])) : '';
    $clientVisible = isset($_POST['client_visible']) ? intval($_POST['client_visible']) : 0;
    $targetsEmail = isset($_POST['targets_email']) ? array_map('intval', (array) $_POST['targets_email']) : [];

    if (isset($_FILES['reply_file']) && !empty($_FILES['reply_file']['name'])) {
        $file = $_FILES['reply_file'];

        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];

        // Vérification côté serveur du vrai type MIME (ne pas se fier à $_FILES['type'])
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $real_mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array(strtolower($real_mime), $allowed_types)) {
            wp_send_json_error(['message' => 'Type de fichier non autorisé.']);
            return;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $upload_errors = [
                UPLOAD_ERR_INI_SIZE   => 'Le fichier est trop grand (dépassant la limite de php.ini).',
                UPLOAD_ERR_FORM_SIZE  => 'Le fichier est trop grand (dépassant la limite de formulaire).',
                UPLOAD_ERR_PARTIAL    => 'Le fichier n\'a été que partiellement téléchargé.',
                UPLOAD_ERR_NO_FILE    => 'Aucun fichier n\'a été téléchargé.',
                UPLOAD_ERR_NO_TMP_DIR => 'Dossier temporaire manquant.',
                UPLOAD_ERR_CANT_WRITE => 'Impossible d\'écrire sur le disque.',
                UPLOAD_ERR_EXTENSION  => 'Une extension PHP a arrêté le téléchargement du fichier.'
            ];
            
            $error_message = isset($upload_errors[$file['error']]) ? $upload_errors[$file['error']] : 'Une erreur inconnue est survenue.';
            wp_send_json_error(['message' => $error_message]);
            return;
        }

        $upload_dir = WP_ANNOTATION_PATH . 'assets/images/replies/';
        
        if (!file_exists($upload_dir)) {
            if (!mkdir($upload_dir, 0755, true)) {
                wp_send_json_error(['message' => 'Impossible de créer le répertoire de destination.']);
                return;
            }
        }

        $unique_id = uniqid();
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);

        $new_file_name = "reply_{$unique_id}.{$file_extension}"; 

        $file_path = $upload_dir . $new_file_name;

        if (!move_uploaded_file($file['tmp_name'], $file_path)) {
            wp_send_json_error(['message' => 'Une erreur est survenue lors du traitement du fichier.']);
            return;
        }    
    } else {
        $new_file_name = '';
    }

    $insert = $wpdb->insert(
        $table_name,
        [
            'comment_id' => $commentID,
            'user_id' => $userID,
            'commentaire' => $commentText,
            'file_path' => $new_file_name,
            'client_visible' => $clientVisible,
        ]
    );    

    if ($insert) {                
        $table_reviews = $wpdb->prefix . 'reviews';
        $new_comment_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_reviews WHERE id = %d", $commentID), ARRAY_A);

        if (!empty($targetsEmail) && $new_comment_data['user_id'] !== get_current_user_id()) {
            sendNotificationEmail(
                $new_comment_data,
                [wp_kses_post(stripslashes($commentText)), $new_file_name],
                $targetsEmail
            );
        }

        ob_start();
        extract($new_comment_data);
        include WP_ANNOTATION_PATH . 'views/frontend/replies/replies-box-content.php';
        $reply_content = ob_get_clean();

        wp_send_json_success([
            'message' => 'Commentaire ajouté avec succès',
            'reply_content' => $reply_content
        ]);
    } else {
        wp_send_json_error(['message' => 'Une erreur est survenue lors de l\'ajout du commentaire.']);
    }
}

add_action('wp_ajax_wp_annotations_submit_reply', 'wp_annotations_submit_reply');

// ########  ######## ##       ######## ######## ########    ########  ######## ########  ##       ##    ## 
// ##     ## ##       ##       ##          ##    ##          ##     ## ##       ##     ## ##        ##  ##  
// ##     ## ##       ##       ##          ##    ##          ##     ## ##       ##     ## ##         ####   
// ##     ## ######   ##       ######      ##    ######      ########  ######   ########  ##          ##    
// ##     ## ##       ##       ##          ##    ##          ##   ##   ##       ##        ##          ##    
// ##     ## ##       ##       ##          ##    ##          ##    ##  ##       ##        ##          ##    
// ########  ######## ######## ########    ##    ########    ##     ## ######## ##        ########    ##    

function wp_annotation_delete_reply() {
    check_ajax_referer( 'wp_annotations_nonce', 'nonce' );

    if ( ! is_user_logged_in() || ! check_user_role() ) {
        wp_send_json_error(['message' => 'Permission refusée.']);
        return;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'reviews_replies';
    $datas = $_POST['datas'];
        
    $id = intval( $datas[0] );
    $filename = $wpdb->get_row($wpdb->prepare("SELECT file_path FROM $table_name WHERE id = %d", $id), ARRAY_A);

    if(!empty($filename['file_path'])){
        $screenUrl = $filename['file_path'];

        $file_path = WP_ANNOTATION_PATH . 'assets/images/replies/' . $screenUrl;
        if (file_exists($file_path)) {
                unlink($file_path);
        }
    }

    $delete = $wpdb->delete(
        $table_name,
        ['id' => $id],
        ['%d']
    );

    if ($delete !== false) {              
        $table_reviews = $wpdb->prefix . 'reviews';
        $new_comment_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_reviews WHERE id = %d", intval($datas[1])), ARRAY_A);

        ob_start();
        extract($new_comment_data);
        include WP_ANNOTATION_PATH . 'views/frontend/replies/replies-box-content.php';
        $reply_content = ob_get_clean();

        wp_send_json_success([
            'message' => 'Réponse supprimée avec succès',
            'reply_content' => $reply_content
        ]);
    } else {
        wp_send_json_error(['message' => 'Une erreur est survenue lors de la suppression de la réponse.']);
    }  
}

add_action('wp_ajax_wp_annotation_delete_reply', 'wp_annotation_delete_reply');

// ##     ## ########  ########     ###    ######## ########    ########     ###     ######  ##     ## ########   #######     ###    ########  ########  
// ##     ## ##     ## ##     ##   ## ##      ##    ##          ##     ##   ## ##   ##    ## ##     ## ##     ## ##     ##   ## ##   ##     ## ##     ## 
// ##     ## ##     ## ##     ##  ##   ##     ##    ##          ##     ##  ##   ##  ##       ##     ## ##     ## ##     ##  ##   ##  ##     ## ##     ## 
// ##     ## ########  ##     ## ##     ##    ##    ######      ##     ## ##     ##  ######  ######### ########  ##     ## ##     ## ########  ##     ## 
// ##     ## ##        ##     ## #########    ##    ##          ##     ## #########       ## ##     ## ##     ## ##     ## ######### ##   ##   ##     ## 
// ##     ## ##        ##     ## ##     ##    ##    ##          ##     ## ##     ## ##    ## ##     ## ##     ## ##     ## ##     ## ##    ##  ##     ## 
//  #######  ##        ########  ##     ##    ##    ########    ########  ##     ##  ######  ##     ## ########   #######  ##     ## ##     ## ########  

function wp_annotations_refresh_dashboard() {
    check_ajax_referer( 'wp_annotations_nonce', 'nonce' );

    if ( ! is_user_logged_in() ) {
        wp_send_json_error(['message' => 'Permission refusée.']);
        return;
    }

    $variables = [
        $view = sanitize_text_field($_POST['view']),
        $viewDevice = sanitize_text_field($_POST['deviceView'])
    ];

    ob_start();            
    extract($variables);
    include WP_ANNOTATION_PATH . 'views/frontend/comments-box.php';
    $comments_content = ob_get_clean(); 

    wp_send_json_success([
        'message' => '',
        'comments_content' => $comments_content
    ]);
}

add_action('wp_ajax_wp_annotations_refresh_dashboard', 'wp_annotations_refresh_dashboard');

// '########:'##:::::::'##::::'##::'######::'##::::'##:
//  ##.....:: ##::::::: ##:::: ##:'##... ##: ##:::: ##:
//  ##::::::: ##::::::: ##:::: ##: ##:::..:: ##:::: ##:
//  ######::: ##::::::: ##:::: ##:. ######:: #########:
//  ##...:::: ##::::::: ##:::: ##::..... ##: ##.... ##:
//  ##::::::: ##::::::: ##:::: ##:'##::: ##: ##:::: ##:
//  ##::::::: ########:. #######::. ######:: ##:::: ##:
// ..::::::::........:::.......::::......:::..:::::..::

// === Delete all comments/replies
function flush_reviews_callback() {
    check_ajax_referer( 'wp_annotations_nonce', 'nonce' );

    if ( ! is_user_logged_in() || ! check_user_role() ) {
        wp_send_json_error(['message' => 'Permission refusée.']);
        return;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'reviews';
    $table_replies = $wpdb->prefix . 'reviews_replies';    
    $directory_path = WP_ANNOTATION_PATH . '/assets/images/screenshots/';    
    $replies_path = WP_ANNOTATION_PATH . '/assets/images/replies/';

    if($_POST['context'] === 'dehactivate'){
        $wpdb->query("DROP TABLE IF EXISTS $table_name");
        $wpdb->query("DROP TABLE IF EXISTS $table_replies");

        delete_option( 'wp_annotation_users' );
        delete_option( 'wp_annotation_enabled' );
        delete_option( 'wp_annotation_color' );
        delete_option( 'wp_annotation_quality' );
        delete_option( 'wp_annotation_smtp_mail' );
        delete_option( 'wp_annotation_smtp_user' );
        delete_option( 'wp_annotation_smtp_password' );
        delete_option( 'wp_annotation_smtp_from_name' );
        delete_option( 'wp_annotation_smtp_from_email' );
        delete_option( 'wp_annotation_smtp_valid' );
    }
    elseif($_POST['context'] === 'flush'){
        $wpdb->query("DELETE FROM $table_name");
        $wpdb->query("DELETE FROM $table_replies");
    }

    if (is_dir($directory_path)) {
        if ($dir = opendir($directory_path)) {
            while (($file = readdir($dir)) !== false) {
                if ($file != '.' && $file != '..') {
                    $file_path = $directory_path . '/' . $file;
                    if (is_file($file_path)) {
                        unlink($file_path);
                    }
                }
            }
            closedir($dir);
        }
    }

    if (is_dir($replies_path)) {
        if ($dir = opendir($replies_path)) {
            while (($file = readdir($dir)) !== false) {
                if ($file != '.' && $file != '..') {
                    $file_path = $replies_path . '/' . $file;
                    if (is_file($file_path)) {
                        unlink($file_path);
                    }
                }
            }
            closedir($dir);
        }
    }

    if($_POST['context'] === 'flush'){
        wp_send_json_success('Tous les commentaires ont été supprimés.');
    
        add_action('admin_notices', function() {
            ?>
            <div class="notice notice-success is-dismissible">
                <p>Tous les commentaires ont été supprimés.</p>
            </div>
            <?php
        });
    }
    else{
        wp_send_json_success();
    }
}
add_action('wp_ajax_flush_reviews', 'flush_reviews_callback');
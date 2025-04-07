<?php
// *** COMMENTS
// Add new comments
function wp_annotation_submit_comment() {
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Vous devez être connecté pour ajouter un commentaire.']);
        return;
    }

    if (isset($_POST['datas']) && is_array($_POST['datas'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'reviews';
        $datas = $_POST['datas'];

        parse_str($datas[0], $form_data);

        $commentaire = isset($form_data['comment']) ? stripslashes(sanitize_text_field($form_data['comment'])) : '';
        $position_x = isset($datas[1]) ? intval($datas[1]) : 0;
        $position_y = isset($datas[2]) ? intval($datas[2]) : 0;
        $device = isset($datas[3]) ? sanitize_text_field($datas[3]) : '';
        $page_id = isset($datas[4]) ? intval($datas[4]) : get_the_ID();
        $user_id = isset($datas[5]) ? intval($datas[5]) : get_current_user_id();

        if (empty($commentaire)) {
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
            $screenshot_data = base64_decode($screenshot_data);

            file_put_contents($file_path, $screenshot_data);
            $screenshot_path = $file_name;
        }

        // Insérer le commentaire dans la DB
        $insert = $wpdb->insert(
            $table_name,
            [
                'commentaire' => $commentaire,
                'position_x' => $position_x,
                'position_y' => $position_y,
                'device' => $device,
                'page_id' => $page_id,
                'user_id' => $user_id,
                'timestamp' => current_time('mysql'),
                'statut' => 'non résolu',
                'screenshot_url' => $screenshot_path
            ]
        );

        if ($insert) {
            ob_start();
            include WP_ANNOTATION_PATH . 'views/frontend/comments-box.php';
            $comments_content = ob_get_clean();

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

add_action('wp_ajax_submit_wp_annotation', 'wp_annotation_submit_comment');

// Update comments
function wp_annotation_update_comment() {
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Vous devez être connecté pour ajouter un commentaire.']);
        return;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'reviews';
    $table_replies = $wpdb->prefix . 'reviews_replies';

    $variables = [
        $view = sanitize_text_field($_POST['view'])
    ];

    if( $_POST['type'] === 'status' ){
        $id = intval($_POST['id']);
        $status = sanitize_text_field($_POST['status']);
        $message = $status == 'Résolu' ? 'Commentaire résolu' : 'Commentaire non résolu';

        $update = $wpdb->update(
            $table_name,
            [ 'statut' => $status ],
            [ 'id' => $id ],
            [ '%s' ],
            [ '%d' ]
        );    
    
        if ($update !== false) {
            ob_start();
            extract($variables);
            include WP_ANNOTATION_PATH . 'views/frontend/comments-box.php';
            $comments_content = ob_get_clean();
    
            wp_send_json_success([
                'message' => $message,
                'comments_content' => $comments_content
            ]);
        } 
        else {
            wp_send_json_error(['message' => 'Une erreur est survenue.']);
        }
    }
    elseif( $_POST['type'] === 'delete' ){
        $id = intval($_POST['id']);
        $screenUrl = sanitize_text_field($_POST['screenUrl']);
        $delete = array();

        $filename = $wpdb->get_row($wpdb->prepare("SELECT screenshot_url FROM $table_name WHERE id = %d", $id), ARRAY_A);
        $screenUrl = $filename['screenshot_url'];
        
        $file_path = WP_ANNOTATION_PATH . 'assets/images/screenshots/' . $screenUrl;
        if (file_exists($file_path)) {
                unlink($file_path);
        }

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

        if ($delete[0] !== false && $delete[1] !== false) {
            
            $file_path = WP_ANNOTATION_PATH . 'assets/images/screenshots/' . $screenUrl;
            if (file_exists($file_path)) {
                unlink($file_path);
            }

            ob_start();
            extract($variables);
            include WP_ANNOTATION_PATH . 'views/frontend/comments-box.php';
            $comments_content = ob_get_clean();
    
            wp_send_json_success([
                'message' => 'Commentaire supprimé',
                'comments_content' => $comments_content
            ]);
        } else {
            wp_send_json_error(['message' => 'Une erreur est survenue lors de la suppression du commentaire.']);
        }
    }
    elseif( $_POST['type'] === 'update' ){
        $id = intval($_POST['id']);
        $comment = sanitize_text_field($_POST['comment']);

        $update = $wpdb->update(
            $table_name,
            [ 'commentaire' => $comment ],
            [ 'id' => $id ],
            [ '%s' ],
            [ '%d' ]
        );    
    
        if ($update !== false) {
            ob_start();
            extract($variables);
            include WP_ANNOTATION_PATH . 'views/frontend/comments-box.php';
            $comments_content = ob_get_clean();
    
            wp_send_json_success([
                'message' => 'Commentaire mis à jour',
                'comments_content' => $comments_content
            ]);
        } 
        else {
            wp_send_json_error(['message' => 'Une erreur est survenue.']);
        }
    }
    elseif( $_POST['type'] === 'refresh' ){
        ob_start();
        extract($variables);
        include WP_ANNOTATION_PATH . 'views/frontend/comments-box.php';
        $comments_content = ob_get_clean();

        wp_send_json_success([
            'comments_content' => $comments_content
        ]);
    }
}

add_action('wp_ajax_update_wp_annotation', 'wp_annotation_update_comment');

// *** REPLIES
// First display
function wp_annotation_show_reply() {
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

add_action('wp_ajax_open_reply_wp_annotation', 'wp_annotation_show_reply');

// Manage replies
function wp_annotation_replies() {
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Vous devez être connecté pour ajouter un commentaire.']);
        return;
    }

    if ( isset($_POST['status'])){
        global $wpdb;
        $table_name = $wpdb->prefix . 'reviews_replies';
        $smtp = get_option('wp_annotation_smtp_valid', false);
    
        if( $_POST['status'] === 'add' ){
            $commentID = isset($_POST['comment_id']) ? intval($_POST['comment_id']) : 0;
            $userID = get_current_user_id();
            $commentText = isset($_POST['comment_text']) ? wp_kses_post(stripslashes($_POST['comment_text'])) : '';
            $notifyEmail = isset($_POST['notify_email']) ? intval($_POST['notify_email']) : 0;
            $targetsEmail = isset($_POST['targets_email']) ? $_POST['targets_email'] : [];

            if (isset($_FILES['reply_file']) && !empty($_FILES['reply_file']['name'])) {
                $file = $_FILES['reply_file'];
    
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
                if (!in_array(strtolower($file['type']), $allowed_types)) {
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
                    if (!mkdir($upload_dir, 0777, true)) {
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
                    'file_path' => $new_file_name
                ]
            );    
        
            if ($insert) {                
                $table_reviews = $wpdb->prefix . 'reviews';
                $new_comment_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_reviews WHERE id = %d", $commentID), ARRAY_A);

                if ($notifyEmail && !empty($targetsEmail) && $new_comment_data['user_id'] !== get_current_user_id() && $smtp) {
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
        elseif( $_POST['status'] === 'delete' ){
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
                    'message' => 'Commentaire supprimé avec succès',
                    'reply_content' => $reply_content
                ]);
            } else {
                wp_send_json_error(['message' => 'Une erreur est survenue lors de la suppression du commentaire.']);
            }  
        }
    } else {
        wp_send_json_error(['message' => 'Une erreur est survenue lors de la suppression du commentaire.']);
    } 
}

add_action('wp_ajax_wp_annotation_replies', 'wp_annotation_replies');

// Delete all comments
function flush_reviews_callback() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'reviews';
    $table_replies = $wpdb->prefix . 'reviews_replies';    
    $directory_path = WP_ANNOTATION_PATH . '/assets/images/screenshots/';    
    $replies_path = WP_ANNOTATION_PATH . '/assets/images/replies/';

    error_log($_POST['context']);

    if($_POST['context'] === 'dehactivate'){
        error_log('dehactivate');
        $wpdb->query("DROP TABLE IF EXISTS $table_name");
        $wpdb->query("DROP TABLE IF EXISTS $table_replies");

        delete_option( 'wp_annotation_users' );
        delete_option( 'wp_annotation_enabled' );
        delete_option( 'wp_annotation_color' );
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
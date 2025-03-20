<?php
if (!defined('ABSPATH')) {
    exit;
}

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

        $delete[] = $wpdb->delete(
            $table_name,
            ['id' => $id],
            ['%d']
        );

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

// *** DISCUSSIONS
// First display
function wp_annotation_show_discussion() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'reviews';

    $comment_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", intval($_POST['id'])), ARRAY_A);

    if (!$comment_data) {
        wp_send_json_error(['message' => 'Commentaire non trouvé.']);
    }

    ob_start();
    extract($comment_data);
    include WP_ANNOTATION_PATH . 'views/frontend/replies/replies-box.php';
    $discussion_content = ob_get_clean();
    
    wp_send_json_success([
        'discussion_content' => $discussion_content
    ]);
}

add_action('wp_ajax_open_discussion_wp_annotation', 'wp_annotation_show_discussion');

// Manage replies
function wp_annotation_replies() {
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Vous devez être connecté pour ajouter un commentaire.']);
        return;
    }

    if ( isset($_POST['datas']) && is_array($_POST['datas']) ){
        global $wpdb;
        $table_name = $wpdb->prefix . 'reviews_replies';
    
        if( $_POST['status'] === 'add' ){
            $datas = $_POST['datas'];

            $insert = $wpdb->insert(
                $table_name,
                [
                    'comment_id' => intval($datas[0]),
                    'user_id' => get_current_user_id(),
                    'commentaire' => isset($datas[2]) ? stripslashes(sanitize_text_field($datas[2])) : ''
                ]
            );    
        
            if ($insert) {                
                $table_reviews = $wpdb->prefix . 'reviews';
                $new_comment_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_reviews WHERE id = %d", intval($datas[0])), ARRAY_A);

                if ( $datas[3] ){
                    sendNotificationEmail(
                        $new_comment_data,
                        stripslashes(sanitize_text_field($datas[2]))
                    );
                }

                ob_start();
                extract($new_comment_data);
                include WP_ANNOTATION_PATH . 'views/frontend/replies/replies-box-content.php';
                $discussion_content = ob_get_clean();
    
                wp_send_json_success([
                    'message' => 'Commentaire ajouté avec succès',
                    'discussion_content' => $discussion_content
                ]);
            } else {
                wp_send_json_error(['message' => 'Une erreur est survenue lors de l\'ajout du commentaire.']);
            }
        }
        elseif( $_POST['status'] === 'delete' ){
            $datas = $_POST['datas'];

            $id = intval( $datas[0] );
    
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
                $discussion_content = ob_get_clean();
    
                wp_send_json_success([
                    'message' => 'Commentaire supprimé avec succès',
                    'discussion_content' => $discussion_content
                ]);
            } else {
                wp_send_json_error(['message' => 'Une erreur est survenue lors de la suppression du commentaire.']);
            }  
        }
        // elseif( $_POST['type'] === 'update' ){
        //     $id = intval($_POST['id']);
        //     $comment = sanitize_text_field($_POST['comment']);
    
        //     $update = $wpdb->update(
        //         $table_name,
        //         [ 'commentaire' => $comment ],
        //         [ 'id' => $id ],
        //         [ '%s' ],
        //         [ '%d' ]
        //     );    
        
        //     if ($update !== false) {
        //         ob_start();
        //         extract($variables);
        //         include WP_ANNOTATION_PATH . 'views/frontend/comments-box.php';
        //         $comments_content = ob_get_clean();
        
        //         wp_send_json_success([
        //             'message' => 'Commentaire mis à jour',
        //             'comments_content' => $comments_content
        //         ]);
        //     } 
        //     else {
        //         wp_send_json_error(['message' => 'Une erreur est survenue.']);
        //     }
        // }
    }
}

add_action('wp_ajax_wp_annotation_replies', 'wp_annotation_replies');


// Delete all comments
function flush_reviews_callback() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'reviews';
    $wpdb->query("DELETE FROM $table_name");

    
    $directory_path = WP_ANNOTATION_PATH . '/assets/images/screenshots/';

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

    wp_send_json_success('Tous les commentaires ont été supprimés.');

    add_action('admin_notices', function() {
        ?>
        <div class="notice notice-success is-dismissible">
            <p>Tous les commentaires ont été supprimés.</p>
        </div>
        <?php
    });
}
add_action('wp_ajax_flush_reviews', 'flush_reviews_callback');
<?php
if (!defined('ABSPATH')) {
    exit;
}

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

        $commentaire = isset($form_data['comment']) ? sanitize_text_field($form_data['comment']) : '';
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
            $upload_dir = WP_PLUGIN_DIR . '/wp-annotations/assets/images/screenshots/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $unique_id = uniqid(); // ID unique pour le fichier
            $file_name = "screen_{$unique_id}.png";
            $file_path = $upload_dir . $file_name;
            
            // Décoder l'image en base64
            $screenshot_data = $_POST['screenshot'];
            $screenshot_data = str_replace('data:image/png;base64,', '', $screenshot_data);
            $screenshot_data = base64_decode($screenshot_data);

            // Sauvegarde de l'image
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

        $delete = $wpdb->delete(
            $table_name,
            ['id' => $id],
            ['%d']
        );

        if ($delete !== false) {
            
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
}

add_action('wp_ajax_update_wp_annotation', 'wp_annotation_update_comment');
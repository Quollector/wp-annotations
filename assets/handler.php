<?php
if ( ! defined( 'ABSPATH' ) ) exit;

//  ######   ######## ########     ######   #######  ##     ## ##     ## ######## ##    ## ########  ######  
// ##    ##  ##          ##       ##    ## ##     ## ###   ### ###   ### ##       ###   ##    ##    ##    ## 
// ##        ##          ##       ##       ##     ## #### #### #### #### ##       ####  ##    ##    ##       
// ##   #### ######      ##       ##       ##     ## ## ### ## ## ### ## ######   ## ## ##    ##     ######  
// ##    ##  ##          ##       ##       ##     ## ##     ## ##     ## ##       ##  ####    ##          ## 
// ##    ##  ##          ##       ##    ## ##     ## ##     ## ##     ## ##       ##   ###    ##    ##    ## 
//  ######   ########    ##        ######   #######  ##     ## ##     ## ######## ##    ##    ##     ######  

function getAllCommentsHandler($view = 'all', $viewDevice = 'all'){
    global $wpdb;
    $table_name = $wpdb->prefix . 'reviews';

    $status = match($view) {
        'active' => 'non résolu',
        'resolved' => 'résolu',
        default => 'all',
    };

    // Whitelist des valeurs autorisées pour l'appareil
    $allowed_devices = ['laptop', 'tablet', 'mobile'];
    if ( ! in_array($viewDevice, $allowed_devices, true) ) {
        $viewDevice = 'all';
    }

    $where  = [];
    $params = [];

    if ( ! check_user_role() ) {
        $where[] = 'client_visible = 1';
    }

    if ( $status !== 'all' ) {
        $where[] = 'statut = %s';
        $params[] = $status;
    }

    if ( $viewDevice !== 'all' ) {
        $where[] = 'device = %s';
        $params[] = $viewDevice;
    }

    $sql = "SELECT * FROM $table_name";
    if ( ! empty($where) ) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }

    $datas = ! empty($params)
        ? $wpdb->get_results( $wpdb->prepare($sql, ...$params) )
        : $wpdb->get_results( $sql );

    $grouped_annotations = [];

    foreach ($datas as $annotation) {
        $grouped_annotations[$annotation->page_id][] = $annotation;
    }

    return $grouped_annotations;
}

//  ######   ######## ########     ######  ########    ###    ########  ######  
// ##    ##  ##          ##       ##    ##    ##      ## ##      ##    ##    ## 
// ##        ##          ##       ##          ##     ##   ##     ##    ##       
// ##   #### ######      ##        ######     ##    ##     ##    ##     ######  
// ##    ##  ##          ##             ##    ##    #########    ##          ## 
// ##    ##  ##          ##       ##    ##    ##    ##     ##    ##    ##    ## 
//  ######   ########    ##        ######     ##    ##     ##    ##     ######  

function getCommentsCountHandler($device = 'all'){
    global $wpdb;
    $table_name = $wpdb->prefix . 'reviews';

    // Whitelist de l'appareil
    $allowed_devices = ['laptop', 'tablet', 'mobile'];
    if ( ! in_array($device, $allowed_devices, true) ) {
        $device = 'all';
    }

    if ( check_user_role() ) {
        $total          = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );
        $count_laptop   = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table_name WHERE device = %s", 'laptop' ) );
        $count_tablet   = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table_name WHERE device = %s", 'tablet' ) );
        $count_mobile   = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table_name WHERE device = %s", 'mobile' ) );
        $count_non_resolu = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table_name WHERE statut = %s", 'non résolu' ) );
        $count_resolu   = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table_name WHERE statut = %s", 'résolu' ) );

        if ( $device !== 'all' ) {
            $count_non_resolu = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table_name WHERE statut = %s AND device = %s", 'non résolu', $device ) );
            $count_resolu     = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table_name WHERE statut = %s AND device = %s", 'résolu', $device ) );
        }
    }
    else {
        $total          = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE client_visible = 1" );
        $count_laptop   = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table_name WHERE device = %s AND client_visible = 1", 'laptop' ) );
        $count_tablet   = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table_name WHERE device = %s AND client_visible = 1", 'tablet' ) );
        $count_mobile   = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table_name WHERE device = %s AND client_visible = 1", 'mobile' ) );
        $count_non_resolu = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table_name WHERE statut = %s AND client_visible = 1", 'non résolu' ) );
        $count_resolu   = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table_name WHERE statut = %s AND client_visible = 1", 'résolu' ) );

        if ( $device !== 'all' ) {
            $count_non_resolu = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table_name WHERE statut = %s AND client_visible = 1 AND device = %s", 'non résolu', $device ) );
            $count_resolu     = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table_name WHERE statut = %s AND client_visible = 1 AND device = %s", 'résolu', $device ) );
        }
    }

    $totalComments = $count_non_resolu + $count_resolu;

    return [
        'total'           => $total,
        'totalComments'   => $totalComments,
        'count_non_resolu' => $count_non_resolu,
        'count_resolu'    => $count_resolu,
        'count_laptop'    => $count_laptop,
        'count_tablet'    => $count_tablet,
        'count_mobile'    => $count_mobile
    ];
}

//  ######   ######## ########    ########  ######## ########  ##       #### ########  ######  
// ##    ##  ##          ##       ##     ## ##       ##     ## ##        ##  ##       ##    ## 
// ##        ##          ##       ##     ## ##       ##     ## ##        ##  ##       ##       
// ##   #### ######      ##       ########  ######   ########  ##        ##  ######    ######  
// ##    ##  ##          ##       ##   ##   ##       ##        ##        ##  ##             ## 
// ##    ##  ##          ##       ##    ##  ##       ##        ##        ##  ##       ##    ## 
//  ######   ########    ##       ##     ## ######## ##        ######## #### ########  ######  

function getAllReplies($comment_id) {
    global $wpdb;
    $table_name_replies = $wpdb->prefix . 'reviews_replies';

    if (check_user_role()) {
        $sql = $wpdb->prepare(
            "SELECT * FROM $table_name_replies WHERE comment_id = %d ORDER BY timestamp ASC",
            $comment_id
        );
    }
    else{
        $sql = $wpdb->prepare(
            "SELECT * FROM $table_name_replies WHERE comment_id = %d AND client_visible = 1 ORDER BY timestamp ASC",
            $comment_id
        );
    }

    return $wpdb->get_results($sql);
}

//  ######   ######## ########    ##     ##  ######  ######## ########   ######     ######## ##     ##    ###    #### ##        ######  
// ##    ##  ##          ##       ##     ## ##    ## ##       ##     ## ##    ##    ##       ###   ###   ## ##    ##  ##       ##    ## 
// ##        ##          ##       ##     ## ##       ##       ##     ## ##          ##       #### ####  ##   ##   ##  ##       ##       
// ##   #### ######      ##       ##     ##  ######  ######   ########   ######     ######   ## ### ## ##     ##  ##  ##        ######  
// ##    ##  ##          ##       ##     ##       ## ##       ##   ##         ##    ##       ##     ## #########  ##  ##             ## 
// ##    ##  ##          ##       ##     ## ##    ## ##       ##    ##  ##    ##    ##       ##     ## ##     ##  ##  ##       ##    ## 
//  ######   ########    ##        #######   ######  ######## ##     ##  ######     ######## ##     ## ##     ## #### ########  ######  

function getUsersEmails($datas, $comment, $notifications){
    $users_array = array();
    $replies = isset($datas['id']) ? getAllReplies($datas['id']) : [];

    $users_array[$datas['user_id']] = false;

    $users_emails = array();

    if(!empty($replies)){
        foreach($replies as $reply){
            $users_array[$reply->user_id] = false;
        }
    }

    if(!empty($notifications)){
        foreach($notifications as $not_id){
            if(get_userdata($not_id)){
                $username = get_userdata($not_id)->display_name;
                $pattern = '/@' . preg_quote($username, '/') . '/';
    
                if(preg_match($pattern, $comment)){
                    $users_array[$not_id] = true;
                }
            }
        }
    }

    foreach($users_array as $id => $notified){
        if($id != get_current_user_id()){
            $user = get_userdata($id);

            if (check_user_role($user)) {
                $users_emails[] = [$user->user_email, $notified];
            }
        }
    }

    return $users_emails;
}
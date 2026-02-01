<?php

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

    if ( check_user_role() ) {
        if( $viewDevice !== 'all' && $status !== 'all' ) {
            $query = "SELECT * FROM $table_name WHERE statut = '$status' AND device = '$viewDevice'";
        }
        elseif( $viewDevice !== 'all' ) {
            $query = "SELECT * FROM $table_name WHERE device = '$viewDevice'";
        }
        elseif( $status !== 'all' ) {
            $query = "SELECT * FROM $table_name WHERE statut = '$status'";
        }
        else {
            $query = "SELECT * FROM $table_name";
        }
    } else{
        if( $viewDevice !== 'all' && $status !== 'all' ) {
            $query = "SELECT * FROM $table_name WHERE statut = '$status' AND client_visible = 1 AND device = '$viewDevice'";
        }
        elseif( $viewDevice !== 'all' ) {
            $query = "SELECT * FROM $table_name WHERE client_visible = 1 AND device = '$viewDevice'";
        }
        elseif( $status !== 'all' ) {
            $query = "SELECT * FROM $table_name WHERE statut = '$status' AND client_visible = 1";
        }
        else {
            $query = "SELECT * FROM $table_name WHERE client_visible = 1";
        }
    }

    $datas = $wpdb->get_results($query);

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

    if ( check_user_role() ) {
        $total = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );
        $count_laptop = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE device = 'laptop'" );
        $count_tablet = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE device = 'tablet'" );
        $count_mobile = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE device = 'mobile'" );
        $count_non_resolu = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE statut = 'non résolu'" );
        $count_resolu = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE statut = 'résolu'" );

        if( $device !== 'all' ){
            $count_non_resolu = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE statut = 'non résolu' AND device = '$device'" );
            $count_resolu = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE statut = 'résolu' AND device = '$device'" );
        }
    }
    else{
        $total = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE client_visible = 1" );
        $count_laptop = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE device = 'laptop' AND client_visible = 1" );
        $count_tablet = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE device = 'tablet' AND client_visible = 1" );
        $count_mobile = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE device = 'mobile' AND client_visible = 1" );
        $count_non_resolu = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE statut = 'non résolu' AND client_visible = 1" );
        $count_resolu = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE statut = 'résolu' AND client_visible = 1" );
        
        if( $device !== 'all' ){
            $count_non_resolu = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE statut = 'non résolu' AND client_visible = 1 AND device = '$device'" );
            $count_resolu = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE statut = 'résolu' AND client_visible = 1 AND device = '$device'" );
        }
    }

    $totalComments = $count_non_resolu + $count_resolu;

    return [
        'total' => $total,
        'totalComments' => $totalComments,
        'count_non_resolu' => $count_non_resolu,
        'count_resolu' => $count_resolu,
        'count_laptop' => $count_laptop,
        'count_tablet' => $count_tablet,
        'count_mobile' => $count_mobile
    ];
}
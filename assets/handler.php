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

    if( $view !== 'all' && $viewDevice !== 'all' ) {
        $query = "SELECT * FROM $table_name WHERE statut = '$status' AND device = '$viewDevice'";
    }
    elseif( $viewDevice !== 'all' ) {
        $query = "SELECT * FROM $table_name WHERE device = '$viewDevice'";
    }
    elseif( $view !== 'all' ) {
        $query = "SELECT * FROM $table_name WHERE statut = '$status'";
    }
    else {
        $query = "SELECT * FROM $table_name";
    }

    $datas = $wpdb->get_results($query);

    $grouped_annotations = [];
    $total = 0;
    $count_non_resolu = 0;
    $count_resolu = 0;
    $count_laptop = 0;
    $count_tablet = 0;
    $count_mobile = 0;

    foreach ($datas as $annotation) {
        $grouped_annotations[$annotation->page_id][] = $annotation;

        if ( check_user_role() ) {
            if ( $annotation->statut === 'non résolu' && get_post($annotation->page_id) ) {
                $count_non_resolu++;
            } elseif ( $annotation->statut === 'résolu' && get_post($annotation->page_id) ) {
                $count_resolu++;
            }

            if ( $annotation->device === 'laptop' && get_post($annotation->page_id) ) {
                $count_laptop++;
            } elseif ( $annotation->device === 'tablet' && get_post($annotation->page_id) ) {
                $count_tablet++;
            } elseif ( $annotation->device === 'mobile' && get_post($annotation->page_id) ) {
                $count_mobile++;
            }

            $total++;
        }
        else{
            if( $annotation->client_visible ) {
                if ( $annotation->statut === 'non résolu' && get_post($annotation->page_id) ) {
                    $count_non_resolu++;
                } elseif ( $annotation->statut === 'résolu' && get_post($annotation->page_id) ) {
                    $count_resolu++;
                }   
            }

            if ( $annotation->device === 'laptop' && get_post($annotation->page_id) ) {
                $count_laptop++;
            } elseif ( $annotation->device === 'tablet' && get_post($annotation->page_id) ) {
                $count_tablet++;
            } elseif ( $annotation->device === 'mobile' && get_post($annotation->page_id) ) {
                $count_mobile++;
            }

            $total++;
        }
    }

    return [
        'annotations' => $grouped_annotations,
        'count_non_resolu' => $count_non_resolu,
        'count_resolu' => $count_resolu,
        'count_laptop' => $count_laptop,
        'count_tablet' => $count_tablet,
        'count_mobile' => $count_mobile,
        'total' => $total
    ];

}

//  ######   ######## ########     ######  ########    ###    ########  ######  
// ##    ##  ##          ##       ##    ##    ##      ## ##      ##    ##    ## 
// ##        ##          ##       ##          ##     ##   ##     ##    ##       
// ##   #### ######      ##        ######     ##    ##     ##    ##     ######  
// ##    ##  ##          ##             ##    ##    #########    ##          ## 
// ##    ##  ##          ##       ##    ##    ##    ##     ##    ##    ##    ## 
//  ######   ########    ##        ######     ##    ##     ##    ##     ######  

function getCommentsCountHandler(){
    global $wpdb;
    $table_name = $wpdb->prefix . 'reviews';

    $total = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );
    $count_non_resolu = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE statut = 'non résolu'" );
    $count_resolu = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE statut = 'résolu'" );
    $count_laptop = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE device = 'laptop'" );
    $count_tablet = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE device = 'tablet'" );
    $count_mobile = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE device = 'mobile'" );

    return [
        'total' => $total,
        'count_non_resolu' => $count_non_resolu,
        'count_resolu' => $count_resolu,
        'count_laptop' => $count_laptop,
        'count_tablet' => $count_tablet,
        'count_mobile' => $count_mobile
    ];
}
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

    if( $view !== 'all' && $viewDevice !== 'all' ) {
        $query = "SELECT * FROM $table_name WHERE statut = '$view' AND device = '$viewDevice'";
    }
    elseif( $viewDevice !== 'all' ) {
        $query = "SELECT * FROM $table_name WHERE device = '$viewDevice'";
    }
    elseif( $view !== 'all' ) {
        $query = "SELECT * FROM $table_name WHERE statut = '$view'";
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
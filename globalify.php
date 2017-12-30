<?php
/**
 * Plugin name: Globalify
 **/
 
if (isset($_GET['lang'])) {
    if ($lang == 'reset') {
        globalify_reset_language();
    }
    globalify_set_language($_GET['lang']);
}

add_filter( 'the_content', 'globalify_filter_the_content_in_the_main_loop' );

function globalify_get_accepted_language() {
    if (isset($_COOKIE['desired_language']) && $_COOKIE['desired_language'] != null) {
        
        return $_COOKIE['desired_language']; 
    }
    return substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
} 

function globalify_set_language($lang) {
    setcookie('desired_language', $lang, mktime() + 60 * 60 * 23 * 365);
}

function globalify_reset_language($lang) {
    setcookie('desired_language', null, mktime() - 1);
}

add_filter( 'the_title', 'globalify_get_title', 10, 2);

function globalify_get_title($title, $id) {
    $lang = globalify_get_accepted_language();
    $value = $title;
    $localized_value = get_post_meta($id, 'title_' . $lang, TRUE);
    
    if ($localized_value != null) {
        $value = $localized_value;
    }
    return $value;
}

function globalify_getqtlangcustomfieldvalue($metadata, $object_id, $meta_key, $single) {
   
    $lang = globalify_get_accepted_language();
   
    $value = '';
   
    //use $wpdb to get the value
    global $wpdb;
    $localized_value = $wpdb->get_var( "SELECT meta_value FROM $wpdb->postmeta WHERE post_id = $object_id AND  meta_key = '".$meta_key."_" . $lang . "'" );
    if ($localized_value != null) {
        
        $value = $localized_value;
    } else {
        
        $value = $wpdb->get_var( "SELECT meta_value FROM $wpdb->postmeta WHERE post_id = $object_id AND  meta_key = '".$meta_key."'" );
    }
    //do whatever with $value

    return $value;
    
}

add_filter('get_post_metadata', 'globalify_getqtlangcustomfieldvalue', 10, 4);

function globalify_filter_the_content_in_the_main_loop( $content ) {
    $lang = globalify_get_accepted_language();
    // Check if we're inside the main loop in a single post page.
        $internationalized_content = get_post_meta(get_the_ID(), 'content_' . $lang, true);
    if ($internationalized_content != null && strlen($internationalized_content) > 0) {
         $content = $internationalized_content;
    }
        

    return $content;
}

function globalify_get_localized_post_meta($post_id, $meta) {
    $lang = globalify_get_accepted_language();
    $value = '';
    if ($lang != 'en') {
        
        $localized_value = get_post_meta($post_id, $meta . '_' . $lang, TRUE);
        if ($localized_value != null && strlen($localized_value) > 0) {
            $value = $localized_value;
        }
    } else {
        $value = get_post_meta($post_id, $meta, TRUE);
    }
    return $value;
}
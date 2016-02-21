<?php

/**
 * Uninstall file as per WP 2.7+
 * 
 */

if ( !defined ('ABSPATH')  || !defined('WP_UNINSTALL_PLUGIN') ) die ( __( 'Sorry, you are not allowed to access this file directly.', 'bs-supersized' ) );

if ( !defined('WP_UNINSTALL_PLUGIN') )
    
    exit(); // silence is golden


define('PATH', dirname(__FILE__));
require_once PATH . '/config/config.php';

global $wpdb;

if (!is_multisite()) {
    delete_option($bs_Supersized_directory_settings_key);
    delete_option($bs_Supersized_gallery_settings_key);
} else {
    //$old_blog = $wpdb->blogid;
    // Get all blog ids
    $blogIds = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
    foreach ($blogIds as $blog_id) {
        switch_to_blog($blog_id);
        delete_option($bs_Supersized_directory_settings_key);
        delete_option($bs_Supersized_gallery_settings_key);
    }
    //switch_to_blog($old_blog);
    restore_current_blog();
}

?>
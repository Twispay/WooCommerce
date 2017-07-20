<?php
/**
 * Twispay Uninstall
 *
 * Uninstalling Twispay deletes user pages, tables, and options.
 *
 * @package  Twispay/Uninstall
 * @category Core
 * @author   @TODO
 * @version  0.0.1
 */

// Exit if the file is accessed directly
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

delete_option( 'tw_installed' );

// Delete All TW Twispay Pages
wp_delete_post( get_page_by_title( 'Twispay confirmation' )->ID );

// Remove All Tables
global $wpdb;

$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "tw_configuration" );
$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "tw_transactions" );
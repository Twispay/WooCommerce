<?php
/**
 * Twispay Uninstall
 *
 * Uninstalling Twispay deletes user pages, tables, and options.
 *
 * @package  Twispay/Uninstall
 * @category Core
 * @author   Twispay
 * @version  1.0.8
 */

// Exit if the file is accessed directly
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

delete_option( 'twispay_tw_installed' );

// Delete All TW Twispay Pages
wp_delete_post( get_page_by_title( 'Twispay confirmation' )->ID );

// Remove All Tables
global $wpdb;

$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "twispay_tw_configuration" );
$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "twispay_tw_transactions" );

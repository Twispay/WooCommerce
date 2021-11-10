<?php
/**
 * Twispay Uninstall
 *
 * Uninstalling Twispay deletes user pages, tables, and options.
 *
 * @package  Twispay/Uninstall
 * @category Core
 * @author   Twispay
 */

// Exit if the file is accessed directly
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

delete_option( 'twispay_tw_installed' );

// Delete All TW Twispay Pages
$page = get_page_by_path('twispay-confirmation');
if ($page) {
	wp_delete_post( $page->ID );
}

// Remove All Tables
global $wpdb;

$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "twispay_tw_configuration" );
$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "twispay_tw_transactions" );

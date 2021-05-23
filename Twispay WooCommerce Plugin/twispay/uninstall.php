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

require_once plugin_dir_path(__FILE__) . 'includes/class-tw-db-tables.php';

// Exit if the file is accessed directly
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

delete_option('twispay_tw_installed');

// Delete All TW Twispay Pages
$page = get_page_by_path('twispay-confirmation');
if ($page) {
	wp_delete_post( $page->ID );
}

$tables = new Twispay_TW_DB_Tables();
$tables->drop_all();

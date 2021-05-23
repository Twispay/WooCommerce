<?php
/**
 * Twispay Install
 *
 * Installing Twispay user pages, tables, and options.
 *
 * @package  Twispay/Install
 * @category Core
 * @author   Twispay
 */

function twispay_wp_check_install() {
    if (!get_option('twispay_tw_installed')) {
        twispay_tw_install();
    }
}

add_action('admin_init', 'twispay_wp_check_install');

function twispay_tw_install() {
    update_option('twispay_tw_installed', '1');

    // Create new pages from Twispay Confirmation with shortcodes included
    wp_insert_post([
        'post_title' => esc_html__('Twispay confirmation', 'tw-confirmation'),
        'post_content' => '[tw_payment_confirmation]',
        'post_status' => 'publish',
        'post_author' => get_current_user_id(),
        'post_type' => 'page',
        'comment_status' => 'closed'
    ]);

    $tables = new Twispay_TW_DB_Tables();
    $tables->install();
}

register_activation_hook(TWISPAY_PLUGIN_DIR, 'twispay_tw_install');


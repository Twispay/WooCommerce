<?php
/**
 * Twispay Admin Menu
 *
 * Setup the admin menus in Wordpress Dashboard
 *
 * @package  Twispay/Admin
 * @category Admin
 * @author   @TODO
 * @version  0.0.1
 */

// Exit if the file is accessed directly
if ( ! defined( 'ABSPATH' ) ) { 
    exit; 
}

// Security class check
if ( ! class_exists( 'TWAdminMenu' ) ) :

/**
 * Dashboard Menus TWAdminMenu Class.
 *
 * @class TWAdminMenu
 * @version	0.0.1
 */
class TWAdminMenu {
    /**
     * TWAdminMenu Constructor
     *
     * Will hook the admin menus in tabs
     *
     * @public
     * @return void
     */
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
    }
    
    /**
     * Function that will add the menus items, as well the submenus
     *
     * @public
     * @return void
     */
    public function admin_menu() {
        // Load languages
        $lang = explode( '-', get_bloginfo( 'language' ) );
        $lang = $lang[0];
        if ( file_exists( TWISPAY_PLUGIN_DIR . 'lang/' . $lang . '/lang.php' ) ) {
            require( TWISPAY_PLUGIN_DIR . 'lang/' . $lang . '/lang.php' );
        } else {
            require( TWISPAY_PLUGIN_DIR . 'lang/en/lang.php' );
        }
        
        // Add main adminsitrator page
        add_menu_page( __( 'Twispay', 'twispay' ), __( $tw_lang['menu_main_title'], 'twispay' ), 'administrator', 'twispay', 'twispay_configuration', 'dashicons-editor-paste-text', 1000 );
        
        // Add submenus
        add_submenu_page( 'twispay', __( $tw_lang['menu_configuration_tab'], 'twispay' ), __( $tw_lang['menu_configuration_tab'], 'twispay' ), 'administrator', 'twispay', 'twispay_configuration' );
        add_submenu_page( 'twispay', __( $tw_lang['menu_transaction_tab'], 'twispay' ), __( $tw_lang['menu_transaction_tab'], 'twispay' ), 'administrator', 'tw-transaction', 'tw_transaction_administrator' );
        add_submenu_page( 'twispay', __( $tw_lang['menu_transaction_log_tab'], 'twispay' ), __( $tw_lang['menu_transaction_log_tab'], 'twispay' ), 'administrator', 'tw-transaction-log', 'tw_transaction_log_administrator' );
    }
}

endif; // End if class_exists

return new TWAdminMenu();
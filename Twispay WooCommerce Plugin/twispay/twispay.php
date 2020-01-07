<?php
/**
 * Plugin Name: Twispay Credit Card Payments
 * Plugin URI: https://wordpress.org/plugins/twispay/
 * Description: Plugin for Twispay payment gateway.
 * Version: 1.0.7
 * Author: twispay
 * Author URI: https://www.twispay.com
 * License: GPLv2
 *
 * Text Domain: twispay
 *
 * @package  Twispay
 * @category Core
 * @author   Twispay
 * @version  1.0.8
 */

// Exit if the file is accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Security class check
if ( ! class_exists( 'Twispay' ) ) :

/**
 * Main Twispay Class.
 */
final class Twispay {
    /**
     * Twispay instance.
     *
     * @private
     * @var    Twispay Instance of class Twispay
     */
    private static $__instance;

    /**
     * Main Twispay Instance
     *
     * Only one instance of Twispay is loaded
     *
     * @static
     * @return Twispay
     */
    public static function instance() {
	if ( ! isset( self::$__instance ) && ! ( self::$__instance instanceof Twispay ) ) {
	    self::$__instance = new self();

	    self::$__instance->twispay_tw_set_objects();
	}

	return self::$__instance;
    }

    /**
     * Twispay Constructor
     *
     * @public
     * @return void
     */
    public function __construct() {
	$this->twispay_tw_set_constants();
	if ( get_option( 'twispay_tw_installed' ) ) {
	    $this->twispay_tw_includes();
	}

	if ( is_admin() ) {
	    require_once TWISPAY_PLUGIN_DIR . 'includes/install.php';
	    require_once TWISPAY_PLUGIN_DIR . 'includes/admin/ma-class-menu.php';
	}
    }

    /**
     * Twispay Constants
     *
     * Set all constants in order to use them later
     *
     * @private
     * @return void
     */
    private function twispay_tw_set_constants() {
	// Set plugin folder
	if ( ! defined( 'TWISPAY_PLUGIN_DIR' ) ) {
	   define( 'TWISPAY_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
	}
    }

    /**
     * Twispay Objects
     *
     * Set all objects in order to use them later
     *
     * @private
     * @return void
     */
    private function twispay_tw_set_objects() {
      if ( get_option( 'twispay_tw_installed' ) ) {
          self::$__instance->payment_confirmation = new Twispay_TW_Payment_Confirmation;
          self::$__instance->views = new Twispay_TW_Views;
      }
    }

    /**
     * Twispay Includes
     *
     * Include required core files used in admin and on the frontend
     *
     * @public
     * @return void
     */
    public function twispay_tw_includes() {
	// Includes all admin required classes
	if ( is_admin() ) {
	    require_once TWISPAY_PLUGIN_DIR . 'includes/admin/configuration/configuration.php';
	    require_once TWISPAY_PLUGIN_DIR . 'includes/admin/configuration/requests.php';
	    require_once TWISPAY_PLUGIN_DIR . 'includes/admin/transaction/transaction.php';
	    require_once TWISPAY_PLUGIN_DIR . 'includes/admin/transaction/requests.php';
	    require_once TWISPAY_PLUGIN_DIR . 'includes/admin/transaction-log/transaction-log.php';
	    require_once TWISPAY_PLUGIN_DIR . 'includes/admin/admin-requests.php';
	}

	// Includes all non-admin classes
	require_once TWISPAY_PLUGIN_DIR . 'includes/scripts.php';
	require_once TWISPAY_PLUGIN_DIR . 'includes/a-functions.php';
	require_once TWISPAY_PLUGIN_DIR . 'includes/class-tw-shortcodes.php';
	require_once TWISPAY_PLUGIN_DIR . 'includes/class-tw-payment-confirmation.php';
	require_once TWISPAY_PLUGIN_DIR . 'includes/class-tw-views.php';
    }
}

endif; // End if class_exists

/**
 * The main instance of Twispay
 *
 * This function is used like a global variable, but without to
 * declare the global
 *
 * @return Twispay
 */
function TW() {
    return Twispay::instance();
}
TW();

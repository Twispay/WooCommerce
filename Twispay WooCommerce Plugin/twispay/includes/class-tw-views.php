<?php
/**
 * Twispay Views
 *
 * Render specific views template
 *
 * @package  Twispay/Front
 * @category Front
 * @author   Twispay
 * @version  1.0.8
 */

// Exit if the file is accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Security class check
if ( ! class_exists( 'Twispay_TW_Views' ) ) :

/**
 * Twispay Views Class
 */
class Twispay_TW_Views {
    /**
     * Twispay_TW_Views Constructor
     *
     * @public
     * @return void
     */
    public function __construct() {

    }

    /**
     * Render the Front Twispay View
     *
     * @public
     * @return string Individual view
     */
    public function twispay_tw_render_view( $slug ) {
	      include TWISPAY_PLUGIN_DIR . 'views/' . $slug . '.php';
    }
}

endif; // End if class_exists

<?php
/**
 * Twispay Shortcodes
 *
 * Here is created and processed all shortcodes for Administrator Pages
 *
 * @package  Twispay/Admin
 * @category Admin
 * @author   Twispay
 */

// Exit if the file is accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Security class check
if ( ! class_exists( 'Twispay_TW_Shortcodes' ) ) :

/**
 * Twispay Shorcodes Class
 */
class Twispay_TW_Shortcodes {
    /**
     * Twispay_TW_Shortcodes Constructor
     *
     * @public
     * @return void
     */
    public function __construct() {
        add_shortcode( 'tw_payment_confirmation', array( $this, 'twispay_tw_payment_confirmation_handler' ) );
    }

    /**
     * Renders the Twispay Payment Confirmation Form
     *
     * @public
     * @return string Payment Confirmation Form
     */
    public function twispay_tw_payment_confirmation_handler( $atts ) {
	    return TW()->payment_confirmation->twispay_tw_payment_confirmation_form();
    }
}

endif; // End if class_exists

/**
 * The main instance of Twispay_TW_Shortcodes
 *
 * @return Twispay_TW_Shortcodes
 */
new Twispay_TW_Shortcodes;

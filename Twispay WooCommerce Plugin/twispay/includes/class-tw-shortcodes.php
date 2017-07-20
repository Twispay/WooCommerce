<?php
/**
 * Twispay Shortcodes
 *
 * Here is created and processed all shortcodes for Administrator Pages
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
if ( ! class_exists( 'TW_Shortcodes' ) ) :

/**
 * Twispay Shorcodes Class
 *
 * @class   TW_Shortcodes
 * @version 0.0.1
 */
class TW_Shortcodes {
    /**
     * TW_Shortcodes Constructor
     *
     * @public
     * @return void
     */
    public function __construct() {
        add_shortcode( 'tw_payment_confirmation', array( $this, 'tw_payment_confirmation_handler' ) );
    }
    
    /**
     * Renders the Twispay Payment Confirmation Form
     *
     * @public
     * @return string Payment Confirmation Form
     */	
    public function tw_payment_confirmation_handler( $atts ) {
	return TW()->payment_confirmation->tw_payment_confirmation_form();
    }
}

endif; // End if class_exists

/**
 * The main instance of TW_Shortcodes
 *
 * @return TW_Shortcodes
 */
new TW_Shortcodes;
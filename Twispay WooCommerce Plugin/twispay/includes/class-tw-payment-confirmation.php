<?php
/**
 * Twispay Payment Confirmation
 *
 * Twispay Payment Confirmation process ( setup_form )
 *
 * @package  Twispay/Front
 * @category Front
 * @author   @TODO
 * @version  0.0.1
 */

// Exit if the file is accessed directly
if ( ! defined( 'ABSPATH' ) ) { 
    exit; 
}

// Security class check
if ( ! class_exists( 'TW_Payment_Confirmation' ) ) :

/**
 * Twispay Payment Confirmation Class
 *
 * @class   TW_Payment_Confirmation
 * @version 0.0.1
 */
class TW_Payment_Confirmation  {
    /**
     * TW_Payment_Confirmation Constructor
     *
     * @public
     * @return void
     */
    public function __construct() {
	
    }
    
    /**
     * Call and render the Twispay Payment Confirmation Form
     *
     * @public
     * @return string Payment Confirmation Form
     */	
    public function tw_payment_confirmation_form() {
	return TW()->views->tw_render_view( 'payment-confirmation' );
    }
}

endif; // End if class_exists
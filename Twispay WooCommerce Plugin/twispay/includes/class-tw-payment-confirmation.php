<?php
/**
 * Twispay Payment Confirmation
 *
 * Twispay Payment Confirmation process ( setup_form )
 *
 * @package  Twispay/Front
 * @category Front
 * @author   Twispay
 */

// Exit if the file is accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Security class check
if ( ! class_exists( 'Twispay_TW_Payment_Confirmation' ) ) :

/**
 * Twispay Payment Confirmation Class
 */
class Twispay_TW_Payment_Confirmation  {
    /**
     * Twispay_TW_Payment_Confirmation Constructor
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
    public function twispay_tw_payment_confirmation_form() {
	return TW()->views->twispay_tw_render_view( 'payment-confirmation' );
    }
}

endif; // End if class_exists

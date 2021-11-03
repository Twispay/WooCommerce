<?php
/**
 * Twispay Helpers
 *
 * Redirects user to the order page.
 *
 * @package  Twispay/Front
 * @category Front
 * @author   Twispay
 */

/* Exit if the file is accessed directly. */
if ( !defined('ABSPATH') ) { exit; }

/* Security class check */
if ( ! class_exists( 'Twispay_TW_Default_Thankyou' ) ) :
    /**
     * Twispay Helper Class
     *
     * Class that redirects user to the order page.
     */
    class Twispay_TW_Default_Thankyou extends WC_Payment_Gateway {
      /**
       * Twispay Gateway Constructor
       *
       * @public
       * @return void
       */
      public function __construct( $order ) {
        wp_safe_redirect( esc_url( $this->get_return_url( $order ) ) );
      }
    }
endif; /* End if class_exists. */

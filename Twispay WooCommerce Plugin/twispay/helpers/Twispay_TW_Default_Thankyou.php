<?php
/**
 * Twispay Helpers
 *
 * Redirects user to the order page.
 *
 * @package  Twispay/Front
 * @category Front
 * @author   Twispay
 * @version  1.0.8
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
        wp_safe_redirect( $this->get_return_url( $order ) );
      }
    }
endif; /* End if class_exists. */

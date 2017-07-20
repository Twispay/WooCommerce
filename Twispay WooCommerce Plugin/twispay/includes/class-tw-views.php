<?php
/**
 * Twispay Views
 *
 * Render specific views template
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
if ( ! class_exists( 'TW_Views' ) ) :

/**
 * Twispay Views Class
 *
 * @class   TW_Views
 * @version 0.0.1
 */
class TW_Views {
    /**
     * TW_Views Constructor
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
    public function tw_render_view( $slug ) {
	include TWISPAY_PLUGIN_DIR . 'views/' . $slug . '.php';
    }
}

endif; // End if class_exists
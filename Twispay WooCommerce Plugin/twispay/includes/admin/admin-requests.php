<?php
/**
 * Twispay Main Request Page
 *
 * Here is processed all actions. They will be sent later to their controllers
 *
 * @package  Twispay/Admin
 * @category Admin
 * @author   Twispay
 * @version  1.0.8
 */

/**
 * Hooks every Twispay actions and process them in order to be
 * sent later to their own controllers.
 *
 * @public
 * @return void
 */
function twispay_tw_main_action() {
    // Check if there is a form process in rolling
    if ( isset( $_REQUEST['tw_general_action'] ) ) {
        $request = $_REQUEST['tw_general_action'];

        // Check if current user have administrator permisions. If not, throw 403 error
        if ( ! current_user_can( 'administrator' ) ) {
            wp_die( __( 'You do not have permission to access this file', 'twispay' ), __( 'Error', 'twispay' ), array( 'response' => 403 ) );
        }

        // Check if you are viewing the WordPress Administration Panels
        if ( ! is_admin() ) {
            wp_die( __( 'You do not have permission to access the file from here', 'twispay' ), __( 'Error', 'twispay' ), array( 'response' => 403 ) );
        }

        // Pass the request to their own controllers. This call is dynamic and have following form. Eg: "tw_" + <the_request_name>. If we want to start the edit the configuration, the request will be like "edit_general_configuration"
        do_action( 'tw_' . $request, $_REQUEST );
    }
}
add_action( 'init', 'twispay_tw_main_action' );

<?php
/**
 * Twispay Payment Transaction Request Page
 *
 * Here is processed all payment transaction actions( refund )
 *
 * @package  Twispay/Admin
 * @category Admin
 * @author   @TODO
 * @version  0.0.1
 */

/**
 * Twispay Refund Transaction
 *
 * Process the Refund Transaction to database
 *
 * @public
 * @return void
 */
function tw_twispay_p_refund_payment_transaction( $request ) {
    if ( isset( $_GET['payment_ad'] ) && $_GET['payment_ad'] ) {
        $transaction_id = $_GET['payment_ad'];
        
        // Get configuration from database
        global $wpdb;
        $apiKey = '';
        $configuration = $wpdb->get_row( "SELECT * FROM " . $wpdb->prefix . "twispay_tw_configuration" );
        
        if ( $configuration ) {
            if ( $configuration->live_mode == 1 ) {
                $apiKey = $configuration->live_key;
                $url = 'https://api.twispay.com/transaction/' . $transaction_id;
            }
            else if ( $configuration->live_mode == 0 ) {
                $apiKey = $configuration->staging_key;
                $url = 'https://api-stage.twispay.com/transaction/' . $transaction_id;
            }
        }
        
        $args = array(
            'method' => 'DELETE',
            'headers' => array(
                'Authorization' => 'Bearer ' . $apiKey
            )
        );
        $response = wp_remote_request( $url, $args );
        
        if ( $response['response']['message'] == 'OK' ) {
            // Redirect to the Transaction list Page with success
            wp_safe_redirect( admin_url( 'admin.php?page=tw-transaction&notice=success_refund' ) );
        }
        else {
            // Redirect to the Transaction list Page with error
            wp_safe_redirect( admin_url( 'admin.php?page=tw-transaction&notice=errorp_refund&emessage=' . str_replace( ' ', '%20', $response['body'] ) ) );
        }
    }
    else {
        // Redirect to the Transaction list Page with error
        wp_safe_redirect( admin_url( 'admin.php?page=tw-transaction&notice=error_refund' ) );
    }
}
add_action( 'tw_refund_payment_transaction', 'tw_twispay_p_refund_payment_transaction' );

/**
 * Twispay Recurring Order
 *
 * Process the Recurring Order to database
 *
 * @public
 * @return void
 */
function tw_twispay_p_recurring_order( $request ) {
    if ( isset( $_GET['order_ad'] ) && $_GET['order_ad'] ) {
        $order_ad = $_GET['order_ad'];
        
        // Get configuration from database
        global $wpdb;
        $apiKey = '';
        $configuration = $wpdb->get_row( "SELECT * FROM " . $wpdb->prefix . "twispay_tw_configuration" );
        
        if ( $configuration ) {
            if ( $configuration->live_mode == 1 ) {
                $apiKey = $configuration->live_key;
                $url = 'https://api.twispay.com/order/' . $order_ad;
            }
            else if ( $configuration->live_mode == 0 ) {
                $apiKey = $configuration->staging_key;
                $url = 'https://api-stage.twispay.com/order/' . $order_ad;
            }
        }
        
        $args = array(
            'method' => 'DELETE',
            'headers' => array(
                'Authorization' => 'Bearer ' . $apiKey
            )
        );
        $response = wp_remote_request( $url, $args );
        
        if ( $response['response']['message'] == 'Success' ) {
            // Redirect to the Transaction list Page with success
            wp_safe_redirect( admin_url( 'admin.php?page=tw-transaction&notice=success_recurring' ) );
        }
        else {
            // Redirect to the Transaction list Page with error
            wp_safe_redirect( admin_url( 'admin.php?page=tw-transaction&notice=errorp_refund&emessage=' . str_replace( ' ', '%20', $response['body'] ) ) );
        }
    }
    else {
        // Redirect to the Transaction list Page with error
        wp_safe_redirect( admin_url( 'admin.php?page=tw-transaction&notice=error_recurring' ) );
    }
}
add_action( 'tw_recurring_order', 'tw_twispay_p_recurring_order' );
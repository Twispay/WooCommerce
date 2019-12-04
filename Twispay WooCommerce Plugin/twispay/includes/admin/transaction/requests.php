<?php
/**
 * Twispay Payment Transaction Request Page
 *
 * Here is processed all payment transaction actions( refund )
 *
 * @package  Twispay/Admin
 * @category Admin
 * @author   Twispay
 * @version  1.0.8
 */


/* Exit if the file is accessed directly. */
if ( !defined('ABSPATH') ) { exit; }

/* Require the "Twispay_TW_Logger" class. */
require_once( TWISPAY_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'Twispay_TW_Logger.php' );
/* Require the "Twispay_TW_Status_Updater" class. */
require_once( TWISPAY_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'Twispay_TW_Status_Updater.php' );


/**
 * Twispay Refund Transaction
 *
 * Process the Refund Transaction to database.
 *
 * @public
 * @return void
 */
function tw_twispay_p_refund_payment_transaction() {
    if ( isset( $_GET['payment_ad'] ) && $_GET['payment_ad'] ) {
        $transaction_id = $_GET['payment_ad'];

        /* Get configuration from database. */
        global $wpdb;
        $apiKey = '';
        $configuration = $wpdb->get_row( "SELECT * FROM " . $wpdb->prefix . "twispay_tw_configuration" );

        if ( $configuration ) {
            if ( $configuration->live_mode == 1 ) {
                $apiKey = $configuration->live_key;
                $url = 'https://api.twispay.com/transaction/' . $transaction_id;
            } else if ( $configuration->live_mode == 0 ) {
                $apiKey = $configuration->staging_key;
                $url = 'https://api-stage.twispay.com/transaction/' . $transaction_id;
            }
        }

        $args = array('method' => 'DELETE', 'headers' => ['accept' => 'application/json', 'Authorization' => $apiKey]);
        $response = wp_remote_request( $url, $args );

        if ( $response['response']['message'] == 'OK' ) {
            /* Redirect to the Transaction list Page with success. */
            wp_safe_redirect( admin_url( 'admin.php?page=tw-transaction&notice=success_refund' ) );
        } else {
            /* Redirect to the Transaction list Page with error. */
            wp_safe_redirect( admin_url( 'admin.php?page=tw-transaction&notice=errorp_refund&emessage=' . rawurlencode($response['body']) ) );
        }
    } else {
        /* Redirect to the Transaction list Page with error. */
        wp_safe_redirect( admin_url( 'admin.php?page=tw-transaction&notice=error_refund' ) );
    }
}
add_action( 'tw_refund_payment_transaction', 'tw_twispay_p_refund_payment_transaction' );


/**
 * Twispay Recurring Order
 *
 * Process the Recurring Order to database.
 *
 * @public
 * @return void
 */
function tw_twispay_p_recurring_order( $request ) {
    if ( isset( $_GET['order_ad'] ) && $_GET['order_ad'] ) {
        $order_ad = $_GET['order_ad'];

        /* Get configuration from database. */
        global $wpdb;
        $apiKey = '';
        $configuration = $wpdb->get_row( "SELECT * FROM " . $wpdb->prefix . "twispay_tw_configuration" );

        if ( $configuration ) {
            if ( $configuration->live_mode == 1 ) {
                $apiKey = $configuration->live_key;
                $url = 'https://api.twispay.com/order/' . $order_ad;
            } else if ( $configuration->live_mode == 0 ) {
                $apiKey = $configuration->staging_key;
                $url = 'https://api-stage.twispay.com/order/' . $order_ad;
            }
        }

        $args = array('method' => 'DELETE', 'headers' => ['accept' => 'application/json', 'Authorization' => $apiKey]);
        $response = wp_remote_request( $url, $args );

        if ( $response['response']['message'] == 'OK' ) {
            /* Redirect to the Transaction list Page with success. */
            wp_safe_redirect( admin_url( 'admin.php?page=tw-transaction&notice=success_recurring' ) );
        } else {
            /* Redirect to the Transaction list Page with error. */
            wp_safe_redirect( admin_url( 'admin.php?page=tw-transaction&notice=errorp_refund&emessage=' . rawurlencode($response['body']) ) );
        }
    } else {
        /* Redirect to the Transaction list Page with error. */
        wp_safe_redirect( admin_url( 'admin.php?page=tw-transaction&notice=error_recurring' ) );
    }
}
add_action( 'tw_recurring_order', 'tw_twispay_p_recurring_order' );


/**
 * Twispay Recurring Order
 *
 * Synchronize the subscription statuses.
 *
 * @public
 * @return void
 */
function tw_twispay_p_synchronize_subscriptions( $request ) {
    /* Get configuration from database. */
    global $wpdb;
    $apiKey = '';
    $configuration = $wpdb->get_row( "SELECT * FROM " . $wpdb->prefix . "twispay_tw_configuration" );

    if ( $configuration ) {
        if ( $configuration->live_mode == 1 ) {
            $apiKey = $configuration->live_key;
            $baseUrl = 'https://api.twispay.com/order?externalOrderId=__EXTERNAL_ORDER_ID__&orderType=recurring&page=1&perPage=1&reverseSorting=0';
        } else if ( $configuration->live_mode == 0 ) {
            $apiKey = $configuration->staging_key;
            $baseUrl = 'https://api-stage.twispay.com/order?externalOrderId=__EXTERNAL_ORDER_ID__&orderType=recurring&page=1&perPage=1&reverseSorting=0';
        }
    }

    /* Load languages. */
    $lang = explode( '-', get_bloginfo( 'language' ) )[0];
    if ( file_exists( TWISPAY_PLUGIN_DIR . 'lang/' . $lang . '/lang.php' ) ) {
        require( TWISPAY_PLUGIN_DIR . 'lang/' . $lang . '/lang.php' );
    } else {
        require( TWISPAY_PLUGIN_DIR . 'lang/en/lang.php' );
    }

    /* Extract all the subscriptions. */
    $subscriptions = wcs_get_subscriptions(['subscriptions_per_page' => -1]);
    $skip = FALSE;

    foreach ($subscriptions as $key => $subscription) {
        /* Reset skip flag. */
        $skip = FALSE;

        /* Create a new cURL session. */
        $channel = curl_init();

        /* Construct the URL. */
        $url = str_replace('__EXTERNAL_ORDER_ID__', $subscription->get_parent_id(), $baseUrl);

        /* Set the URL and other needed fields. */
        curl_setopt($channel, CURLOPT_URL, $url);
        curl_setopt($channel, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($channel, CURLOPT_HEADER, FALSE);
        curl_setopt($channel, CURLOPT_HTTPHEADER, ['accept: application/json', 'Authorization: ' . $apiKey]);

        /* Execute the request. This means to perform a "GET"/"PUT" request at the specified URL. */
        $response = curl_exec($channel);

        /* Check if the CURL call failed. */
        if(FALSE === $response) {
            Twispay_TW_Logger::twispay_tw_log( $tw_lang['subscriptions_log_error_call_failed'] . curl_error($channel) );
            $skip = TRUE;
        }

        if((FALSE == $skip) && (200 != curl_getinfo($channel, CURLINFO_HTTP_CODE))){
            Twispay_TW_Logger::twispay_tw_log( $tw_lang['subscriptions_log_error_http_code'] . curl_getinfo($channel, CURLINFO_HTTP_CODE) );
            $skip = TRUE;
        }

        /* Close the cURL session. */
        curl_close($channel);

        if(FALSE == $skip){
            $response = json_decode($response);

            if ( 'Success' == $response->message ) {
                /* Check if any order was found on the server. */
                if($response->pagination->currentItemCount){
                    /* Synchronize the statuses. */
                    Twispay_TW_Status_Updater::updateSubscriptionStatus($subscription->get_parent_id(), $response->data[0]->orderStatus, $tw_lang);
                } else {
                    /* Cancel the local subscription as no order was found on the server. */
                    Twispay_TW_Status_Updater::updateSubscriptionStatus($subscription->get_parent_id(), Twispay_TW_Status_Updater::$RESULT_STATUSES['CANCEL_OK'], $tw_lang);
                }

                /* Redirect to the Transaction list Page with success. */
                wp_safe_redirect( admin_url( 'admin.php?page=tw-transaction&notice=success_recurring' ) );
            } else {
                Twispay_TW_Logger::twispay_tw_log( $tw_lang['subscriptions_log_error_get_status'] . $subscription->get_parent_id() );
            }
        }
    }
    /* Redirect to the Transaction list Page with message. */
    wp_safe_redirect( admin_url( 'admin.php?page=tw-transaction&notice=sync_finished' ) );
}
add_action( 'tw_synchronize_subscriptions', 'tw_twispay_p_synchronize_subscriptions' );

<?php
/**
 * Twispay Payment Validation
 *
 *
 * @package  Twispay/Front
 * @category Front
 * @author   @TODO
 * @version  0.0.1
 */

$parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );
require_once( $parse_uri[0] . 'wp-load.php' );

/* Load languages */
$lang = explode( '-', get_bloginfo( 'language' ) )[0];
if ( file_exists( TWISPAY_PLUGIN_DIR . 'lang/' . $lang . '/lang.php' ) ){
    require( TWISPAY_PLUGIN_DIR . 'lang/' . $lang . '/lang.php' );
} else {
    require( TWISPAY_PLUGIN_DIR . 'lang/en/lang.php' );
}

/* Require the "Twispay_TW_Helper_Response" class. */
require_once( TWISPAY_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'Twispay_TW_Helper_Response.php' );


/* Check if the POST is corrupted: Doesn't contain the 'opensslResult' and the 'result' fields. */
if( (FALSE == isset($_POST['opensslResult'])) && (FALSE == isset($_POST['result'])) ) {
    Twispay_TW_Helper_Response::twispay_tw_log($tw_lang['log_error_empty_response']);
    exit();
}


/* Get configuration from database. */
global $wpdb;
$configuration = $wpdb->get_row( "SELECT * FROM " . $wpdb->prefix . "twispay_tw_configuration" );


$secretKey = '';
if ( $configuration ) {
    if ( 1 == $configuration->live_mode ) {
        $secretKey = $configuration->live_key;
    } else if ( 0 == $configuration->live_mode ) {
        $secretKey = $configuration->staging_key;
    } else {
        /* TODO: Error? */
    }
}


/* Check if there is NO secret key. */
if ( '' == $secretKey ) {
    Twispay_TW_Helper_Response::twispay_tw_log($tw_lang['log_error_invalid_private']);
    exit();
}


/* Extract the server response and decript it. */
$decrypted = Twispay_TW_Helper_Response::twispay_tw_decrypt_message(/*tw_encryptedResponse*/(isset($_POST['opensslResult'])) ? ($_POST['opensslResult']) : ($_POST['result']), $secretKey);


/* Check if decryption failed.  */
if(FALSE === $decrypted){
  Twispay_TW_Helper_Response::twispay_tw_log($tw_lang['log_error_decryption_error']);
  Twispay_TW_Helper_Response::twispay_tw_log($tw_lang['log_error_openssl'] . (isset($_POST['opensslResult'])) ? ($_POST['opensslResult']) : ($_POST['result']));
  Twispay_TW_Helper_Response::twispay_tw_log($tw_lang['log_error_decrypted_string'] . $decrypted );
  exit();
}


/* Validate the decripted response. */
$orderValidation = Twispay_TW_Helper_Response::twispay_tw_checkValidation($decrypted, /*tw_usingOpenssl*/TRUE, $tw_lang);


/* Check if server sesponse validation failed.  */
if(TRUE !== $orderValidation){
    Twispay_TW_Helper_Response::twispay_tw_log($tw_lang['log_error_validating_failed'] );
    exit();
}


/* Extract the WooCommerce order. */
$order = wc_get_order($decrypted['externalOrderId']);


/* Check if the WooCommerce order extraction failed. */
if( FALSE == $order ){
    Twispay_TW_Helper_Response::twispay_tw_log($tw_lang['log_error_invalid_order']);
    exit();
}


/* Extract the transaction status. */
$status = (empty($decrypted['status'])) ? ($decrypted['transactionStatus']) : ($decrypted['status']);


/* Set the status of the WooCommerce order according to the received status. */
switch ($status) {
    case Twispay_TW_Helper_Response::$RESULT_STATUSES['COMPLETE_FAIL']:
        /* Mark order as failed. */
        $order->update_status('failed', __( $tw_lang['wa_order_failed_notice'], 'woocommerce' ));

        Twispay_TW_Helper_Response::twispay_tw_log($tw_lang['log_ok_status_failed'] . $decrypted['externalOrderId']);
    break;

    case Twispay_TW_Helper_Response::$RESULT_STATUSES['CANCEL_OK']:
    case Twispay_TW_Helper_Response::$RESULT_STATUSES['REFUND_OK']:
    case Twispay_TW_Helper_Response::$RESULT_STATUSES['VOID_OK']:
        /* Mark order as refunded. */
        $order->update_status('refunded', __( $tw_lang['wa_order_refunded_notice'], 'woocommerce' ));

        Twispay_TW_Helper_Response::twispay_tw_log($tw_lang['log_ok_status_refund'] . $decrypted['externalOrderId']);
    break;

    case Twispay_TW_Helper_Response::$RESULT_STATUSES['THREE_D_PENDING']:
        /* Mark order as on-hold. */
        $order->update_status('on-hold', __( $tw_lang['wa_order_hold_notice'], 'woocommerce' ));

        Twispay_TW_Helper_Response::twispay_tw_log($tw_lang['log_ok_status_hold'] . $decrypted['externalOrderId']);
    break;

    case Twispay_TW_Helper_Response::$RESULT_STATUSES['IN_PROGRESS']:
    case Twispay_TW_Helper_Response::$RESULT_STATUSES['COMPLETE_OK']:
        /* Mark order as completed. */
        $order->update_status('processing', __( $tw_lang['wa_order_status_notice'], 'woocommerce' ));

        Twispay_TW_Helper_Response::twispay_tw_log($tw_lang['log_ok_status_complete'] . $decrypted['externalOrderId']);
    break;

    default:
      Twispay_TW_Helper_Response::twispay_tw_log($tw_lang['log_error_wrong_status'] . $status);
    break;
}

exit();

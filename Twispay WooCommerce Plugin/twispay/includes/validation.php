<?php
/**
 * Twispay Payment Validation
 *
 *
 * @package  Twispay/Front
 * @category Front
 * @author   Twispay
 * @version  1.0.8
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

/* Require the "Twispay_TW_Logger" class. */
require_once( TWISPAY_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'Twispay_TW_Logger.php' );
/* Require the "Twispay_TW_Helper_Response" class. */
require_once( TWISPAY_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'Twispay_TW_Helper_Response.php' );
/* Require the "Twispay_TW_Status_Updater" class. */
require_once( TWISPAY_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'Twispay_TW_Status_Updater.php' );


/* Check if the POST is corrupted: Doesn't contain the 'opensslResult' and the 'result' fields. */
if( (FALSE == isset($_POST['opensslResult'])) && (FALSE == isset($_POST['result'])) ) {
    Twispay_TW_Logger::twispay_tw_log($tw_lang['log_error_empty_response']);
    die($tw_lang['log_error_empty_response']);
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
    Twispay_TW_Logger::twispay_tw_log($tw_lang['log_error_invalid_private']);
    die($tw_lang['log_error_invalid_private']);
}


/* Extract the server response and decript it. */
$decrypted = Twispay_TW_Helper_Response::twispay_tw_decrypt_message(/*tw_encryptedResponse*/(isset($_POST['opensslResult'])) ? ($_POST['opensslResult']) : ($_POST['result']), $secretKey, $tw_lang);


/* Check if decryption failed.  */
if(FALSE === $decrypted){
    Twispay_TW_Logger::twispay_tw_log($tw_lang['log_error_decryption_error']);
    Twispay_TW_Logger::twispay_tw_log($tw_lang['log_error_openssl'] . (isset($_POST['opensslResult'])) ? ($_POST['opensslResult']) : ($_POST['result']));
    die($tw_lang['log_error_decryption_error']);
} else {
    Twispay_TW_Logger::twispay_tw_log($tw_lang['log_ok_string_decrypted']);
}


/* Validate the decripted response. */
$orderValidation = Twispay_TW_Helper_Response::twispay_tw_checkValidation($decrypted, $tw_lang);


/* Check if server sesponse validation failed.  */
if(TRUE !== $orderValidation){
    Twispay_TW_Logger::twispay_tw_log($tw_lang['log_error_validating_failed']);
    die($tw_lang['log_error_validating_failed']);
}


/* Extract the WooCommerce order. */
$orderId = explode('_', $decrypted['externalOrderId'])[0];
$order = wc_get_order($orderId);


/* Check if the WooCommerce order extraction failed. */
if( FALSE == $order ){
    Twispay_TW_Logger::twispay_tw_log($tw_lang['log_error_invalid_order']);
    die($tw_lang['log_error_invalid_order']);
}


/* Extract the transaction status. */
$status = (empty($decrypted['status'])) ? ($decrypted['transactionStatus']) : ($decrypted['status']);


/* Set the status of the WooCommerce order according to the received status. */
Twispay_TW_Status_Updater::updateStatus_IPN($orderId, $status, $tw_lang);

/* Send the 200 OK response back to the Twispay server. */
die('OK');

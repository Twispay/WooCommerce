<?php
/**
 * Twispay Helpers
 *
 * Decodes and validates notifications sent by the Twispay server.
 *
 * @package  Twispay/Front
 * @category Front
 * @author   @TODO
 * @version  0.0.1
 */

/* Exit if the file is accessed directly. */
if ( !defined('ABSPATH') ) { exit; }

/* Security class check */
if ( ! class_exists( 'Twispay_TW_Helper_Response' ) ) :
    /**
     * Twispay Helper Class
     *
     * @class   Twispay_TW_Helper_Response
     * @version 0.0.1
     *
     *
     * Class that implements methods to decrypt
     * Twispay server responses.
     */
    class Twispay_TW_Helper_Response{
        /* Array containing the possible result statuses. */
        private static $RESULT_STATUSES = ['complete-ok'];


        /**
         * Function that logs a transaction to the DB.
         *
         * @param data Array containing the transaction data.
         *
         * @return void
         */
        private static function twispay_tw_logTransaction( $data ) {
            global $wpdb;
            global $woocommerce;

            try {
                $order = new WC_Order( $data['id_cart'] );
            }
            catch( Exception $e ) {}

            $checkout_url = $woocommerce->cart->get_checkout_url() . 'order-pay/' . $data['id_cart'] . '/?pay_for_order=true&key=' . $order->get_data()['order_key'] . '&tw_reload=true';

            /* Update the DB with the transaction data. */
            $already = $wpdb->get_row( "SELECT * FROM " . $wpdb->prefix . "twispay_tw_transactions WHERE transactionId = '" . $data['transactionId'] . "'" );
            if ( $already ) {
                $wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "twispay_tw_transactions SET status = '" . $data['status'] . "' WHERE transactionId = '%d'", $data['transactionId'] ) );
            } else {
                $wpdb->get_results( "INSERT INTO `" . $wpdb->prefix . "twispay_tw_transactions` (`status`, `id_cart`, `identifier`, `orderId`, `transactionId`, `customerId`, `cardId`, `checkout_url`) VALUES ('" . $data['status'] . "', '" . $data['id_cart'] . "', '" . $data['identifier'] . "', '" . $data['orderId'] . "', '" . $data['transactionId'] . "', '" . $data['customerId'] . "', '" . $data['cardId'] . "', '" . $checkout_url . "');" );
            }
        }


        /**
         * Function that logs a message to the log file.
         *
         * @param string Message to log to file
         *
         * @return void
         */
        public static function twispay_tw_log( $string = false ) {
            $log_file = dirname( __FILE__ ) . '/../twispay-log.txt';
            /* Build the log message. */
            $string = (!$string) ? (PHP_EOL . PHP_EOL) : ("[" . date( 'Y-m-d H:i:s' ) . "] " . $string);

            /* Try to append log to file and silence and PHP errors may occur. */
            @file_put_contents( $log_file, $string . PHP_EOL, FILE_APPEND );
        }


        /**
         * Decrypt the response from Twispay server.
         *
         * @param string $tw_encryptedResponse
         * @param string $tw_secretKey The secret key (from Twispay).
         *
         * @return array
         */
        public static function twispay_tw_decrypt_response($tw_encryptedResponse, $tw_secretKey){
            $encrypted = ( string )$tw_encryptedResponse;

            if ( !strlen($encrypted) || (FALSE == strpos($encrypted, ',')) ){
                return NULL;
            }

            /* Get the IV and the encrypted data */
            $encryptedParts = explode(/*delimiter*/',', $tw_encryptedResponse, /*limit*/2);
            $iv = base64_decode($encryptedParts[0]);
            if ( FALSE === $iv ){
                return FALSE;
            }

            $encryptedData = base64_decode($encryptedParts[1]);
            if ( FALSE === $encryptedData ){
                return FALSE;
            }

            /* Decrypt the encrypted data */
            $decryptedResponse = openssl_decrypt($encryptedData, /*method*/'aes-256-cbc', $tw_secretKey, /*options*/OPENSSL_RAW_DATA, $iv);
            if ( FALSE === $decryptedResponse ){
                return FALSE;
            }

            /* JSON decode the decrypted data. */
            return json_decode($decryptedResponse, /*assoc*/true, /*depth*/4);
        }


        /**
         * Function that validates a decripted response.
         *
         * @param tw_response The server decripted and JSON decoded response
         * @param tw_usingOpenssl Flag marking if the response was encoded or not
         * @param tw_lang The language that the store uses
         *
         * @return array
         */
        public static function twispay_tw_checkValidation($tw_response, $tw_usingOpenssl = true, $tw_lang) {
            $tw_errors = array();

            if ( !$tw_response ) {
                return false;
            }

            if ( FALSE == $tw_usingOpenssl ) {
               Twispay_TW_Helper_Response::twispay_tw_log( $tw_lang['log_s_decrypted'] . $tw_response );
            }

            if ( empty( $tw_response['status'] ) && empty( $tw_response['transactionStatus'] ) ) {
               $tw_errors[] = $tw_lang['log_empty_status'];
            }

            if ( empty( $tw_response['identifier'] ) ) {
                $tw_errors[] = $tw_lang['log_empty_identifier'];
            }

            if ( empty( $tw_response['externalOrderId'] ) ) {
                $tw_errors[] = $tw_lang['log_empty_external'];
            }

            if ( empty( $tw_response['transactionId'] ) ) {
                $tw_errors[] = $tw_lang['log_empty_transaction'];
            }

            if ( sizeof( $tw_errors ) ) {
                foreach ( $tw_errors as $err ) {
                    Twispay_TW_Helper_Response::twispay_tw_log( $tw_lang['log_general_error'] . $err );
                }

                return false;
            } else {
                $data = [ 'id_cart'          => explode( '_', $tw_response['externalOrderId'] )[0]
                        , 'status'           => ( empty($tw_response['status']) ) ? ($tw_response['transactionStatus']) : ($tw_response['status'])
                        , 'identifier'       => $tw_response['identifier']
                        , 'orderId'          => ( int )$tw_response['orderId']
                        , 'transactionId'    => ( int )$tw_response['transactionId']
                        , 'customerId'       => ( int )$tw_response['customerId']
                        , 'cardId'           => ( !empty($tw_response['cardId']) ) ? (( int )$tw_response['cardId']) : (0)];
                Twispay_TW_Helper_Response::twispay_tw_log( $tw_lang['log_general_response_data'] . json_encode( $data ) );

                if ( !in_array($data['status'], Twispay_TW_Helper_Response::$RESULT_STATUSES) ) {
                    Twispay_TW_Helper_Response::twispay_tw_log( sprintf( $tw_lang['log_wrong_status'], $data['status'] ) );
                    Twispay_TW_Helper_Response::twispay_tw_logTransaction( $data );

                    return '0x1ds';
                }
                Twispay_TW_Helper_Response::twispay_tw_log( $tw_lang['log_status_complete'] );
                Twispay_TW_Helper_Response::twispay_tw_logTransaction( $data );
                Twispay_TW_Helper_Response::twispay_tw_log( sprintf( $tw_lang['log_validating_complete'], $data['id_cart'] ) );

                return true;
            }
        }
    }
endif; /* End if class_exists. */

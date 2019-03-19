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

/* Require the "Twispay_TW_Logger" class. */
require_once( TWISPAY_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'Twispay_TW_Logger.php' );

/* Security class check */
if ( !class_exists( 'Twispay_TW_Helper_Response' ) ) :
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
        /**
         * Decrypt the response from Twispay server.
         *
         * @param string $tw_encryptedMessage - The encripted server message.
         * @param string $tw_secretKey        - The secret key (from Twispay).
         *
         * @return Array([key => value,]) - If everything is ok array containing the decrypted data.
         *         bool(FALSE)            - If decription fails.
         */
        public static function twispay_tw_decrypt_message($tw_encryptedMessage, $tw_secretKey){
            $encrypted = ( string )$tw_encryptedMessage;

            if ( !strlen($encrypted) || (FALSE == strpos($encrypted, ',')) ){
                return FALSE;
            }

            /* Get the IV and the encrypted data */
            $encryptedParts = explode(/*delimiter*/',', $encrypted, /*limit*/2);
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
            return json_decode($decryptedResponse, /*assoc*/TRUE, /*depth*/4);
        }


        /**
         * Function that validates a decripted response.
         *
         * @param tw_response The server decripted and JSON decoded response
         * @param tw_usingOpenssl Flag marking if the response is encoded or not
         * @param tw_lang The language that the store uses
         *
         * @return bool(FALSE)     - If any error occurs
         *         bool(TRUE)      - If the validation is successful
         */
        public static function twispay_tw_checkValidation($tw_response, $tw_usingOpenssl = TRUE, $tw_lang) {
            $tw_errors = array();

            if ( !$tw_response ) {
                return FALSE;
            }

            if ( FALSE == $tw_usingOpenssl ) {
               Twispay_TW_Logger::twispay_tw_log($tw_lang['log_ok_string_decrypted'] . $tw_response);
            }

            if ( empty( $tw_response['status'] ) && empty( $tw_response['transactionStatus'] ) ) {
               $tw_errors[] = $tw_lang['log_error_empty_status'];
            }

            if ( empty( $tw_response['identifier'] ) ) {
                $tw_errors[] = $tw_lang['log_error_empty_identifier'];
            }

            if ( empty( $tw_response['externalOrderId'] ) ) {
                $tw_errors[] = $tw_lang['log_error_empty_external'];
            }

            if ( empty( $tw_response['transactionId'] ) ) {
                $tw_errors[] = $tw_lang['log_error_empty_transaction'];
            }

            if ( sizeof( $tw_errors ) ) {
                foreach ( $tw_errors as $err ) {
                    Twispay_TW_Logger::twispay_tw_log( $err );
                }

                return FALSE;
            } else {
                $data = [ 'id_cart'          => explode('_', $tw_response['externalOrderId'])[0]
                        , 'status'           => (empty($tw_response['status'])) ? ($tw_response['transactionStatus']) : ($tw_response['status'])
                        , 'identifier'       => $tw_response['identifier']
                        , 'orderId'          => (int)$tw_response['orderId']
                        , 'transactionId'    => (int)$tw_response['transactionId']
                        , 'customerId'       => (int)$tw_response['customerId']
                        , 'cardId'           => (!empty($tw_response['cardId'])) ? (( int )$tw_response['cardId']) : (0)];

                Twispay_TW_Logger::twispay_tw_log($tw_lang['log_ok_response_data'] . json_encode($data));

                if ( !in_array($data['status'], Twispay_TW_Status_Updater::$RESULT_STATUSES) ) {
                    Twispay_TW_Logger::twispay_tw_log($tw_lang['log_error_wrong_status'] . $data['status']);
                    Twispay_TW_Logger::twispay_tw_logTransaction( $data );

                    return FALSE;
                }
                error_log( "4" . print_r($data, 'true') );
                Twispay_TW_Logger::twispay_tw_logTransaction( $data );
                Twispay_TW_Logger::twispay_tw_log( $tw_lang['log_ok_validating_complete'] . $data['id_cart'] );

                return TRUE;
            }
        }
    }
endif; /* End if class_exists. */

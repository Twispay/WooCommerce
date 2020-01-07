<?php
/**
 * Twispay Helpers
 *
 * Decodes and validates notifications sent by the Twispay server.
 *
 * @package  Twispay/Front
 * @category Front
 * @author   Twispay
 * @version  1.0.8
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
     * Class that implements methods to decrypt
     * Twispay server responses.
     */
    class Twispay_TW_Helper_Response{
        /**
         * Decrypt the response from Twispay server.
         *
         * @param string $tw_encryptedMessage - The encripted server message.
         * @param string $tw_secretKey        - The secret key (from Twispay).
         * @param array $tw_lang              - The language that the store uses
         *
         * @return Array([key => value,]) - If everything is ok array containing the decrypted data.
         *         bool(FALSE)            - If decription fails.
         */
        public static function twispay_tw_decrypt_message($tw_encryptedMessage, $tw_secretKey, $tw_lang){
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

            /** JSON decode the decrypted data. */
            $decodedResponse = json_decode($decryptedResponse, /*assoc*/TRUE, /*depth*/4);

            /** Check if the decryption was successful. */
              if (NULL === $decodedResponse) {
                /** Log the last error occurred during the last JSON encoding/decoding. */
                switch (json_last_error()) {
                    case JSON_ERROR_DEPTH:
                        Twispay_TW_Logger::twispay_tw_log($tw_lang['JSON_ERROR_DEPTH']);
                    break;

                    case JSON_ERROR_STATE_MISMATCH:
                        Twispay_TW_Logger::twispay_tw_log($tw_lang['JSON_ERROR_STATE_MISMATCH']);
                    break;

                    case JSON_ERROR_CTRL_CHAR:
                        Twispay_TW_Logger::twispay_tw_log($tw_lang['JSON_ERROR_CTRL_CHAR']);
                    break;

                    case JSON_ERROR_SYNTAX:
                        Twispay_TW_Logger::twispay_tw_log($tw_lang['JSON_ERROR_SYNTAX']);
                    break;

                    case JSON_ERROR_UTF8:
                        Twispay_TW_Logger::twispay_tw_log($tw_lang['JSON_ERROR_UTF8']);
                    break;

                    case JSON_ERROR_RECURSION:
                        Twispay_TW_Logger::twispay_tw_log($tw_lang['JSON_ERROR_RECURSION']);
                    break;

                    case JSON_ERROR_INF_OR_NAN:
                        Twispay_TW_Logger::twispay_tw_log($tw_lang['JSON_ERROR_INF_OR_NAN']);
                    break;

                    case JSON_ERROR_UNSUPPORTED_TYPE:
                        Twispay_TW_Logger::twispay_tw_log($tw_lang['JSON_ERROR_UNSUPPORTED_TYPE']);
                    break;

                    case JSON_ERROR_INVALID_PROPERTY_NAME:
                        Twispay_TW_Logger::twispay_tw_log($tw_lang['JSON_ERROR_INVALID_PROPERTY_NAME']);
                    break;

                    case JSON_ERROR_UTF16:
                        Twispay_TW_Logger::twispay_tw_log($tw_lang['JSON_ERROR_UTF16']);
                    break;

                    default:
                        Twispay_TW_Logger::twispay_tw_log($tw_lang['JSON_ERROR_UNKNOWN']);
                    break;
                }

                return FALSE;
            }

            /** Check if externalOrderId uses '_' separator */
            if (FALSE !== strpos($decodedResponse['externalOrderId'], '_')) {
                $explodedVal = explode('_', $decodedResponse['externalOrderId'])[0];

                /** Check if externalOrderId contains only digits and is not empty. */
                if (!empty($explodedVal) && ctype_digit($explodedVal)) {
                    $decodedResponse['externalOrderId'] = $explodedVal;
                }
            }

            return $decodedResponse;
        }


        /**
         * Function that validates a decripted response.
         *
         * @param tw_response The server decripted and JSON decoded response
         * @param tw_lang The language that the store uses
         *
         * @return bool(FALSE)     - If any error occurs
         *         bool(TRUE)      - If the validation is successful
         */
        public static function twispay_tw_checkValidation($tw_response, $tw_lang) {
            $tw_errors = array();

            if ( !$tw_response ) {
                return FALSE;
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

                Twispay_TW_Logger::twispay_tw_logTransaction( $data );
                Twispay_TW_Logger::twispay_tw_log( $tw_lang['log_ok_validating_complete'] . $data['id_cart'] );

                return TRUE;
            }
        }
    }
endif; /* End if class_exists. */

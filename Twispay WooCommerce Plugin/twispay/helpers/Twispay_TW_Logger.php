<?php
/**
 * Twispay Helpers
 *
 * Logs messages and transactions.
 *
 * @package  Twispay/Front
 * @category Front
 * @author   Twispay
 * @version  1.0.8
 */

/* Exit if the file is accessed directly. */
if ( !defined('ABSPATH') ) { exit; }

/* Security class check */
if ( ! class_exists( 'Twispay_TW_Logger' ) ) :
    /**
     * Twispay Helper Class
     *
     * Class that implements methods to log
     * messages and transactions.
     */
    class Twispay_TW_Logger{
        /**
         * Function that logs a transaction to the DB.
         *
         * @param data Array containing the transaction data.
         *
         * @return void
         */
        public static function twispay_tw_logTransaction( $data ) {
            global $wpdb;

            /* Extract the WooCommerce order. */
            $order = wc_get_order($data['id_cart']);

            $already = $wpdb->get_row( "SELECT * FROM " . $wpdb->prefix . "twispay_tw_transactions WHERE transactionId = '" . $data['transactionId'] . "'" );
            if ( $already ) {
                /* Update the DB with the transaction data. */
                $wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "twispay_tw_transactions SET status = '" . $data['status'] . "' WHERE transactionId = '%d'", $data['transactionId'] ) );
            } else {
                $checkout_url = ((false !== $order) && (true !== $order)) ? (wc_get_checkout_url() . 'order-pay/' . explode('_', $data['id_cart'])[0] . '/?pay_for_order=true&key=' . $order->get_data()['order_key']) : ("");
                $wpdb->get_results( "INSERT INTO `" . $wpdb->prefix . "twispay_tw_transactions` (`status`, `id_cart`, `identifier`, `orderId`, `transactionId`, `customerId`, `cardId`, `checkout_url`) VALUES ('" . $data['status'] . "', '" . $data['id_cart'] . "', '" . $data['identifier'] . "', '" . $data['orderId'] . "', '" . $data['transactionId'] . "', '" . $data['customerId'] . "', '" . $data['cardId'] . "', '" . $checkout_url . "');" );
            }
        }


        /**
         * Function that updates a transaction's status in the DB.
         *
         * @param id The ID of the parent order.
         * @param status The new status of the transaction.
         *
         * @return void
         */
        public static function twispay_tw_updateTransactionStatus( $id, $status ) {
            global $wpdb;

            $already = $wpdb->get_row( "SELECT * FROM " . $wpdb->prefix . "twispay_tw_transactions WHERE id_cart = '" . $id . "'" );
            if ( $already ) {
                /* Update the DB with the transaction data. */
                $wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "twispay_tw_transactions SET status = '" . $status . "' WHERE id_cart = '%d'", $id ) );
            }
        }


        /**
         * Function that logs a message to the log file.
         *
         * @param string - Message to log to file.
         *
         * @return Void
         */
        public static function twispay_tw_log( $message = FALSE ) {
            $log_file = dirname( __FILE__ ) . '/../twispay-log.txt';
            /* Build the log message. */
            $message = (!$message) ? (PHP_EOL . PHP_EOL) : ("[" . date( 'Y-m-d H:i:s' ) . "] " . $message);

            /* Try to append log to file and silence and PHP errors may occur. */
            @file_put_contents( $log_file, $message . PHP_EOL, FILE_APPEND );
        }
    }
endif; /* End if class_exists. */

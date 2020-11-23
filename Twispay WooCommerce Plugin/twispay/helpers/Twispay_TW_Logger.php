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
if (!defined('ABSPATH')) {
    exit;
}

/* Security class check */
if (!class_exists('Twispay_TW_Logger')) :
    /**
     * Twispay Helper Class
     *
     * Class that implements methods to log
     * messages and transactions.
     */
    class Twispay_TW_Logger
    {
        const LOGS_PATH_OPTION_KEY = 'twispay_logs_path';

        /**
         * Function that logs a transaction to the DB.
         *
         * @param data Array containing the transaction data.
         *
         * @return void
         */
        public static function twispay_tw_logTransaction($data)
        {
            global $wpdb;

            /* Extract the WooCommerce order. */
            $order = wc_get_order($data['id_cart']);

            $already = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "twispay_tw_transactions WHERE transactionId = '" . $data['transactionId'] . "'");
            if ($already) {
                /* Update the DB with the transaction data. */
                $wpdb->query($wpdb->prepare("UPDATE " . $wpdb->prefix . "twispay_tw_transactions SET status = '" . $data['status'] . "' WHERE transactionId = '%d'", $data['transactionId']));
            } else {
                $checkout_url = ((false !== $order) && (true !== $order)) ? (wc_get_checkout_url() . 'order-pay/' . explode('_', $data['id_cart'])[0] . '/?pay_for_order=true&key=' . $order->get_data()['order_key']) : ("");
                $wpdb->get_results("INSERT INTO `" . $wpdb->prefix . "twispay_tw_transactions` (`status`, `id_cart`, `identifier`, `orderId`, `transactionId`, `customerId`, `cardId`, `checkout_url`) VALUES ('" . $data['status'] . "', '" . $data['id_cart'] . "', '" . $data['identifier'] . "', '" . $data['orderId'] . "', '" . $data['transactionId'] . "', '" . $data['customerId'] . "', '" . $data['cardId'] . "', '" . $checkout_url . "');");
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
        public static function twispay_tw_updateTransactionStatus($id, $status)
        {
            global $wpdb;

            $already = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "twispay_tw_transactions WHERE id_cart = '" . $id . "'");
            if ($already) {
                /* Update the DB with the transaction data. */
                $wpdb->query($wpdb->prepare("UPDATE " . $wpdb->prefix . "twispay_tw_transactions SET status = '" . $status . "' WHERE id_cart = '%d'", $id));
            }
        }


        /**
         * Function that logs a message to the log file.
         *
         * @param string - Message to log to file.
         *
         * @return Void
         */
        public static function twispay_tw_log($message = false)
        {
            self::migrate_old_logs();

            /* Build the log message. */
            $message = (!$message) ? (PHP_EOL . PHP_EOL) : ("[" . date('Y-m-d H:i:s') . "] " . $message);

            /* Try to append log to file and silence and PHP errors may occur. */
            @file_put_contents(self::twispay_get_log_file_path(), $message . PHP_EOL, FILE_APPEND);
        }

        /**
         * Migrate the old log file to the new location
         */
        private static function migrate_old_logs()
        {
            if (get_option('twispay_log_is_migrated', false)) {
                return;
            }

            if (is_readable(TWISPAY_PLUGIN_DIR . 'twispay-log.txt')) {
                update_option('twispay_log_is_migrated', 1);
                @rename(TWISPAY_PLUGIN_DIR . 'twispay-log.txt', self::twispay_get_log_file_path());
            }
        }

        /**
         * Generate a random dir name path to keep logs.
         *
         * @return string
         */
        public static function twispay_get_log_dir_path()
        {
            $savedPath = get_option(self::LOGS_PATH_OPTION_KEY, false);

            if ($savedPath && is_dir($savedPath)) {
                return $savedPath;
            }

            $upload_dir = wp_upload_dir();

            $logsDir = $upload_dir['basedir'];
            $logsDir .= '/twispay-' . substr(sha1(rand(PHP_INT_MIN, PHP_INT_MAX) . microtime() . get_bloginfo('url')), 0, 6);
            $logsDir .= '/';

            if (!is_dir($logsDir)) {
                mkdir($logsDir, 0777, true);
            }

            update_option(self::LOGS_PATH_OPTION_KEY, $logsDir);

            return $logsDir;
        }

        /**
         * Log file path
         *
         * @return string
         */
        public static function twispay_get_log_file_path()
        {
            return self::twispay_get_log_dir_path() . '/twispay.log';
        }

        /**
         * @return bool
         */
        public static function twispay_has_logs()
        {
            return file_exists(self::twispay_get_log_file_path());
        }

        /**
         * Reads the log file
         *
         * @return false|string
         */
        public static function twispay_get_logs()
        {
            return file_get_contents(self::twispay_get_log_file_path());
        }

        /**
         * Remove a directory
         *
         * @param $folder
         */
        private static function twispay_remove_folder($folder)
        {
            foreach (new DirectoryIterator($folder) as $f) {
                if ($f->isDot()) {
                    continue;
                }

                if ($f->isFile()) {
                    unlink($f->getPathname());
                    continue;
                }

                if ($f->isDir()) {
                    self::twispay_remove_folder($f->getPathname());
                    continue;
                }
            }

            rmdir($folder);
        }

        /**
         * Clear Twispay logs folder
         */
        public static function twispay_clear_logs()
        {
            self::twispay_remove_folder(self::twispay_get_log_dir_path());
        }
    }
endif; /* End if class_exists. */

<?php

class Twispay_TW_Server_To_Server {
    private $language;

    public function __construct() {
        require_once TWISPAY_PLUGIN_DIR . 'helpers/Twispay_TW_Logger.php';
        require_once TWISPAY_PLUGIN_DIR . 'helpers/Twispay_TW_Helper_Response.php';
        require_once TWISPAY_PLUGIN_DIR . 'helpers/Twispay_TW_Status_Updater.php';
        require_once TWISPAY_PLUGIN_DIR . 'helpers/Twispay_TW_Helper_Processor.php';

        $this->order_id = isset($_GET['order_id']) ? (int) sanitize_key($_GET['order_id']) : null;
        $this->language = Twispay_TW_Helper_Processor::get_current_language();

        if (isset($_GET['twispay-ipn'])) {
            add_action('init', [ $this, 'handle' ]);
        }
    }

    public function handle() {
        // FIXME: Change this i18n logic with the idiomatic one.
        if (file_exists(TWISPAY_PLUGIN_DIR . 'lang/' . $this->language . '/lang.php')) {
            require(TWISPAY_PLUGIN_DIR . 'lang/' . $this->language . '/lang.php');
        } else {
            require(TWISPAY_PLUGIN_DIR . 'lang/en/lang.php');
        }

	    /** @var array $tw_lang */

        // Check if the POST is corrupted: doesn't contain the 'opensslResult' and the 'result' fields.
        if (isset($_POST['opensslResult']) === false && isset($_POST['result']) === false) {
	        Twispay_TW_Logger::twispay_tw_log($tw_lang['log_error_empty_response']);
            die($tw_lang['log_error_empty_response']);
        }

        $configuration = Twispay_TW_Helper_Processor::get_configuration();

        if (empty($configuration)) {
            Twispay_TW_Logger::twispay_tw_log($tw_lang['log_error_invalid_private']);
            die(esc_html($tw_lang['log_error_invalid_private']));
        }

        $result = isset($_POST['opensslResult']) ? sanitize_text_field($_POST['opensslResult']) : sanitize_text_field($_POST['result']);
        $decrypted = Twispay_TW_Helper_Response::twispay_tw_decrypt_message(
            $result,
            $configuration['secret_key'],
            $tw_lang
        );

        if ($decrypted === false) {
            Twispay_TW_Logger::twispay_tw_log($tw_lang['log_error_decryption_error']);
            Twispay_TW_Logger::twispay_tw_log($tw_lang['log_error_openssl'] . $result);

            die(esc_html($tw_lang['log_error_decryption_error']));
        }

        Twispay_TW_Logger::twispay_tw_log($tw_lang['log_ok_string_decrypted']);

        $is_order_valid = Twispay_TW_Helper_Response::twispay_tw_checkValidation($decrypted, $tw_lang);

        if ($is_order_valid !== true) {
            Twispay_TW_Logger::twispay_tw_log($tw_lang['log_error_validating_failed']);
            die(esc_html($tw_lang['log_error_validating_failed']));
        }

        $order_id = (int) explode('_', $decrypted['externalOrderId'])[0];
        $order = wc_get_order($order_id);

        if ($order === false) {
            Twispay_TW_Logger::twispay_tw_log($tw_lang['log_error_invalid_order']);
            die(esc_html($tw_lang['log_error_invalid_order']));
        }

        // Extract the transaction status.
        $status = empty($decrypted['status']) ? $decrypted['transactionStatus'] : $decrypted['status'];

        // Set the status of the WooCommerce order according to the received status.
        Twispay_TW_Status_Updater::updateStatus_IPN($order_id, $status, $tw_lang);

        // Send the 200 OK response back to the Twispay server.
        die('OK');
    }
}

<?php
/**
 * Twispay Language Configurator
 *
 * Twispay general language handler for everything
 *
 * @package  Twispay/Language
 * @category Admin/Front
 * @author   Twispay
 */

/* Configuration panel from Administrator */
$tw_lang['no_woocommerce_f'] = 'Twispay requires WooCommerce plugin to work normally. Please activate it or install it from';
$tw_lang['no_woocommerce_s'] = 'here';
$tw_lang['configuration_title'] = 'Configuration';
$tw_lang['configuration_edit_notice'] = 'Configuration has been edited successfully.';
$tw_lang['configuration_subtitle'] = 'Twispay general settings.';
$tw_lang['live_mode_label'] = 'Live mode';
$tw_lang['live_mode_desc'] = 'Select "Yes" if you want to use the payment gateway in Production Mode or "No" if you want to use it in Staging Mode.';
$tw_lang['staging_id_label'] = 'Staging Site ID';
$tw_lang['staging_id_desc'] = 'Enter the Site ID for Staging Mode. You can get one from';
$tw_lang['staging_key_label'] = 'Staging Private Key';
$tw_lang['staging_key_desc'] = 'Enter the Private Key for Staging Mode. You can get one from';
$tw_lang['live_id_label'] = 'Live Site ID';
$tw_lang['live_id_desc'] = 'Enter the Site ID for Live Mode. You can get one from';
$tw_lang['live_key_label'] = 'Live Private Key';
$tw_lang['live_key_desc'] = 'Enter the Private Key for Live Mode. You can get one from';
$tw_lang['s_t_s_notification_label'] = 'Server-to-server notification URL';
$tw_lang['s_t_s_notification_desc'] = 'Put this URL in your Twispay account.';
$tw_lang['r_custom_thankyou_label'] = 'Redirect to custom Thank you page';
$tw_lang['r_custom_thankyou_desc_f'] = 'If you want to display custom Thank you page, set it up here. You can create new custom page from';
$tw_lang['r_custom_thankyou_desc_s'] = 'here';
$tw_lang['suppress_email_label'] = 'Suppress default WooCommerce payment receipt emails';
$tw_lang['suppress_email_desc'] = 'Option to suppress the communication sent by the ecommerce system, in order to configure it from Twispay’s Merchant interface.';
$tw_lang['configuration_save_button'] = 'Save changes';
$tw_lang['live_mode_option_true'] = 'Yes';
$tw_lang['live_mode_option_false'] = 'No';
$tw_lang['get_all_wordpress_pages_default'] = 'Default';
$tw_lang['contact_email_o'] = 'Contact email(Optional)';
$tw_lang['contact_email_o_desc'] = 'This email will be used on the payment error page.';


/* Transaction list from Administrator */
$tw_lang['transaction_title'] = 'Transaction list';
$tw_lang['transaction_list_search_title'] = 'Search Order';
$tw_lang['transaction_list_all_views'] = 'All';
$tw_lang['transaction_list_refund_title'] = 'Refund transaction';
$tw_lang['transaction_list_recurring_title'] = 'Cancel recurring on this order';
$tw_lang['transaction_list_id'] = 'ID';
$tw_lang['transaction_list_id_cart'] = 'Order reference';
$tw_lang['transaction_list_customer_name'] = 'Customer name';
$tw_lang['transaction_list_transactionId'] = 'Transaction ID';
$tw_lang['transaction_list_status'] = 'Status';
$tw_lang['transaction_list_checkout_url'] = 'Checkout url';
$tw_lang['transaction_list_refund_ptitle'] = 'Refund Payment Transaction';
$tw_lang['transaction_list_refund_subtitle'] = 'Following payment transaction will be refunded:';
$tw_lang['transaction_list_confirm_title'] = 'Confirm';
$tw_lang['transaction_error_refund'] = 'Refund could not been processed.';
$tw_lang['transaction_error_recurring'] = 'Recurring could not been processed.';
$tw_lang['transaction_success_refund'] = 'Refund processed successfully. Refresh the page in seconds to see the update.';
$tw_lang['transaction_success_recurring'] = 'Recurring processed successfully.';
$tw_lang['transaction_list_recurring_ptitle'] = 'Cancel a recurring order';
$tw_lang['transaction_list_recurring_subtitle'] = 'Following recurring order will be canceled:';
$tw_lang['transaction_sync_finished'] = 'Subscriptions synchronization finished.';


/* Transaction log from Administrator */
$tw_lang['transaction_log_title'] = 'Transaction log';
$tw_lang['transaction_log_no_log'] = 'No log recorded yet.';
$tw_lang['transaction_log_subtitle'] = 'Transaction log in raw form.';


/* Administrator Dashboard left-side menu */
$tw_lang['menu_main_title'] = 'Twispay';
$tw_lang['menu_configuration_tab'] = 'Configuration';
$tw_lang['menu_transaction_tab'] = 'Transaction list';
$tw_lang['menu_transaction_log_tab'] = 'Transaction log';


/* Woocommerce settings Twispay tab */
$tw_lang['ws_title'] = 'Twispay';
$tw_lang['ws_description'] = 'Have your customers pay with Twispay payment gateway.';
$tw_lang['ws_enable_disable_title'] = 'Enable/Disable';
$tw_lang['ws_enable_disable_label'] = 'Enable Twispay Payments';
$tw_lang['ws_title_title'] = 'Title';
$tw_lang['ws_title_desc'] = 'This controls the title which the customer sees during checkout.';
$tw_lang['ws_description_title'] = 'Description';
$tw_lang['ws_description_desc'] = 'This controls the description which the customer sees during checkout.';
$tw_lang['ws_description_default'] = 'One integration, multiple payment methods. Twispay enables you to accept payments from virtually anywhere in the world through a myriad of payment methods.';
$tw_lang['ws_enable_methods_title'] = 'Enable for shipping methods';
$tw_lang['ws_enable_methods_desc'] = 'If Twispay is only available for certain shipping methods, set it up here. Leave blank to enable for all methods.';
$tw_lang['ws_enable_methods_placeholder'] = 'Select shipping methods';
$tw_lang['ws_vorder_title'] = 'Accept for virtual orders';
$tw_lang['ws_vorder_desc'] = 'Accept Twispay if the order is virtual';


/* Order Recieved Confirmation title */
$tw_lang['order_confirmation_title'] = 'Thank you. Your transaction is approved.';


/* Twispay Processor( Redirect page to Twispay ) */
$tw_lang['twispay_processor_error_general'] = 'You are not allowed to access this file.';
$tw_lang['twispay_processor_error_no_item'] = 'The order has no items.';
$tw_lang['twispay_processor_error_more_items'] = 'Orders with subscriptions cannot have other products too.';
$tw_lang['twispay_processor_error_missing_configuration'] = 'Missing configuration for plugin.';


/* Validation LOG insertor */
$tw_lang['log_ok_string_decrypted'] = '[RESPONSE]: Decryption successfully performed.';
$tw_lang['log_ok_response_data'] = '[RESPONSE]: Data: ';
$tw_lang['log_ok_status_complete'] = '[RESPONSE]: Status complete-ok for order ID: ';
$tw_lang['log_ok_status_refund'] = '[RESPONSE]: Status refund-ok for order ID: ';
$tw_lang['log_ok_status_failed'] = '[RESPONSE]: Status failed for order ID: ';
$tw_lang['log_ok_status_hold'] = '[RESPONSE]: Status on-hold for order ID: ';
$tw_lang['log_ok_status_uncertain'] = '[RESPONSE]: Status uncertain for order ID: ';
$tw_lang['log_ok_validating_complete'] = '[RESPONSE]: Validating completed for order ID: ';

$tw_lang['log_error_validating_failed'] = '[RESPONSE-ERROR]: Validation failed.';
$tw_lang['log_error_decryption_error'] = '[RESPONSE-ERROR]: Decryption failed.';
$tw_lang['log_error_invalid_order'] = '[RESPONSE-ERROR]: Order does not exist.';
$tw_lang['log_error_wrong_status'] = '[RESPONSE-ERROR]: Wrong status: ';
$tw_lang['log_error_empty_status'] = '[RESPONSE-ERROR]: Empty status';
$tw_lang['log_error_empty_identifier'] = '[RESPONSE-ERROR]: Empty identifier';
$tw_lang['log_error_empty_external'] = '[RESPONSE-ERROR]: Empty externalOrderId';
$tw_lang['log_error_empty_transaction'] = '[RESPONSE-ERROR]: Empty transactionId';
$tw_lang['log_error_empty_response'] = ' [RESPONSE-ERROR]: Received empty response.';
$tw_lang['log_error_invalid_private'] = '[RESPONSE-ERROR]: Private key is not valid.';
$tw_lang['log_error_invalid_key'] = '[RESPONSE-ERROR]: Invalid order identification key.';
$tw_lang['log_error_openssl'] = '[RESPONSE-ERROR]: opensslResult: ';


/* Subscriptions section */
$tw_lang['subscriptions_sync_label'] = 'Synchronize subscriptions';
$tw_lang['subscriptions_sync_desc'] = 'Synchronize the local status of all subscriptions with the server status.';
$tw_lang['subscriptions_sync_button'] = 'Synchronize';
$tw_lang['subscriptions_log_ok_set_status'] = '[RESPONSE]: Server status set for order ID: ';
$tw_lang['subscriptions_log_error_set_status'] = '[RESPONSE-ERROR]: Failed to set server status for order ID: ';
$tw_lang['subscriptions_log_error_get_status'] = '[RESPONSE-ERROR]: Failed to get server status for order ID: ';
$tw_lang['subscriptions_log_error_call_failed'] = '[RESPONSE-ERROR]: Failed to call server: ';
$tw_lang['subscriptions_log_error_http_code'] = '[RESPONSE-ERROR]: Unexpected HTTP response code: ';


/* Wordpress Administrator Order Notice */
$tw_lang['wa_order_status_notice'] = 'Twispay payment finalised successfully';
$tw_lang['wa_order_refunded_notice'] = 'Website manager pressed on refund button successfully';
$tw_lang['wa_order_cancelled_notice'] = 'Website manager pressed on cancel button successfully';
$tw_lang['wa_order_failed_notice'] = 'Twispay payment failed';
$tw_lang['wa_order_hold_notice'] = 'Twispay payment is on hold';


/* Others */
$tw_lang['general_error_title'] = 'An error occurred:';
$tw_lang['general_error_desc_f'] = 'The payment could not be processed. Please';
$tw_lang['general_error_desc_try_again'] = ' try again';
$tw_lang['general_error_desc_or'] = ' or';
$tw_lang['general_error_desc_contact'] = ' contact';
$tw_lang['general_error_desc_s'] = ' the website administrator.';
$tw_lang['general_error_hold_notice'] = ' Payment is on hold.';
$tw_lang['general_error_invalid_key'] = ' Invalid secure key.';
$tw_lang['general_error_invalid_order'] = ' Order does not exist.';
$tw_lang['general_error_invalid_private'] = ' Private key is not valid.';


/* JSON decoding/encoding errors */
$tw_lang['JSON_ERROR_DEPTH'] = 'The maximum stack depth has been exceeded.';
$tw_lang['JSON_ERROR_STATE_MISMATCH'] = 'Invalid or malformed JSON.';
$tw_lang['JSON_ERROR_CTRL_CHAR'] = 'Control character error, possibly incorrectly encoded.';
$tw_lang['JSON_ERROR_SYNTAX'] = 'Syntax error.';
$tw_lang['JSON_ERROR_UTF8'] = 'Malformed UTF-8 characters, possibly incorrectly encoded.';
$tw_lang['JSON_ERROR_RECURSION'] = 'One or more recursive references in the value to be encoded.';
$tw_lang['JSON_ERROR_INF_OR_NAN'] = 'One or more NAN or INF values in the value to be encoded.';
$tw_lang['JSON_ERROR_UNSUPPORTED_TYPE'] = 'A value of a type that cannot be encoded was given.';
$tw_lang['JSON_ERROR_INVALID_PROPERTY_NAME'] = 'A property name that cannot be encoded was given.';
$tw_lang['JSON_ERROR_UTF16'] = 'Malformed UTF-16 characters, possibly incorrectly encoded.';
$tw_lang['JSON_ERROR_UNKNOWN'] = 'Unknown error.';

$tw_lang['default_description'] = 'Pay with twispay';
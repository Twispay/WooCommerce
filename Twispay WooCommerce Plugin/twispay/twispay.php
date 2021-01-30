<?php
/**
 * Plugin Name: Twispay Credit Card Payments
 * Plugin URI: https://wordpress.org/plugins/twispay/
 * Description: Plugin for Twispay payment gateway.
 * Version: 1.0.11
 * Author: twispay
 * Author URI: https://www.twispay.com
 * License: GPLv2
 *
 * Text Domain: twispay
 *
 * @package  Twispay
 * @category Core
 * @author   Twispay
 * @version  1.0.9
 */

// Exit if the file is accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Security class check
if ( ! class_exists( 'Twispay' ) ) :

/**
 * Main Twispay Class.
 */
final class Twispay {
    /**
     * Twispay instance.
     *
     * @private
     * @var    Twispay Instance of class Twispay
     */
    private static $__instance;

    /**
     * Main Twispay Instance
     *
     * Only one instance of Twispay is loaded
     *
     * @static
     * @return Twispay
     */
    public static function instance() {
	if ( ! isset( self::$__instance ) && ! ( self::$__instance instanceof Twispay ) ) {
	    self::$__instance = new self();

	    self::$__instance->twispay_tw_set_objects();
	}

	return self::$__instance;
    }

    /**
     * Twispay Constructor
     *
     * @public
     * @return void
     */
    public function __construct() {
        $this->twispay_tw_set_constants();
        if ( get_option( 'twispay_tw_installed' ) ) {
            $this->twispay_tw_includes();
        }

        if ( is_admin() ) {
            require_once TWISPAY_PLUGIN_DIR . 'includes/install.php';
            require_once TWISPAY_PLUGIN_DIR . 'includes/admin/ma-class-menu.php';
        }

        add_filter( 'query_vars', array( $this, 'twispay_query_vars_filter' ) );

        if( isset( $_GET['order_id'] ) && strpos( sanitize_text_field( $_GET['order_id'] ), '_sub' ) === false ) {
            add_action('woocommerce_after_checkout_form', array( $this, 'twispay_processor' ) );
        }
        if( isset( $_GET['order_id'] ) && strpos( sanitize_text_field( $_GET['order_id'] ), '_sub' ) !== false) {
            add_action('woocommerce_after_checkout_form', array( $this, 'twispay_subscription_processor' ) );
        }
        if( isset( $_GET['server_to_server'] ) && sanitize_text_field( $_GET['server_to_server'] ) === 'true' ) {
            $this->server_to_server();
        }
    }

    /**
     * Twispay Constants
     *
     * Set all constants in order to use them later
     *
     * @private
     * @return void
     */
    private function twispay_tw_set_constants() {
	    // Set plugin folder
        if ( ! defined( 'TWISPAY_PLUGIN_DIR' ) ) {
            define( 'TWISPAY_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
        }

        if ( ! defined( 'TWISPAY_PLUGIN_URL' ) ) {
            define( 'TWISPAY_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
        }
    }

    /**
     * Twispay Objects
     *
     * Set all objects in order to use them later
     *
     * @private
     * @return void
     */
    private function twispay_tw_set_objects() {
      if ( get_option( 'twispay_tw_installed' ) ) {
          self::$__instance->payment_confirmation = new Twispay_TW_Payment_Confirmation;
          self::$__instance->views = new Twispay_TW_Views;
      }
    }

    /**
     * Twispay Includes
     *
     * Include required core files used in admin and on the frontend
     *
     * @public
     * @return void
     */
    public function twispay_tw_includes() {
	// Includes all admin required classes
	if ( is_admin() ) {
	    require_once TWISPAY_PLUGIN_DIR . 'includes/admin/configuration/configuration.php';
	    require_once TWISPAY_PLUGIN_DIR . 'includes/admin/configuration/requests.php';
	    require_once TWISPAY_PLUGIN_DIR . 'includes/admin/transaction/transaction.php';
	    require_once TWISPAY_PLUGIN_DIR . 'includes/admin/transaction/requests.php';
	    require_once TWISPAY_PLUGIN_DIR . 'includes/admin/transaction-log/transaction-log.php';
	    require_once TWISPAY_PLUGIN_DIR . 'includes/admin/admin-requests.php';
	}

	// Includes all non-admin classes
	require_once TWISPAY_PLUGIN_DIR . 'includes/scripts.php';
	require_once TWISPAY_PLUGIN_DIR . 'includes/a-functions.php';
	require_once TWISPAY_PLUGIN_DIR . 'includes/class-tw-shortcodes.php';
	require_once TWISPAY_PLUGIN_DIR . 'includes/class-tw-payment-confirmation.php';
	require_once TWISPAY_PLUGIN_DIR . 'includes/class-tw-views.php';
    }

    public function twispay_query_vars_filter( $vars ) {
        $vars[] .= 'order_id';
        $vars[] .= 'server_to_server';
        return $vars;
    }

    public function twispay_processor() {

        $str = '<style>
                body {
                    height: 100%;
                    overflow: hidden !important;
                }
                .wrapper-loader{
                    background-color: #fff;
                    height: 100%;
                    left: 0;
                    position: absolute;
                    width: 100%;
                    top: 0;
                    z-index: 1000;
                }
                .loader {
                    margin: 15% auto 0;
                    border: 14px solid #f3f3f3;
                    border-top: 14px solid #3498db;
                    border-radius: 50%;
                    width: 110px;
                    height: 110px;
                    animation: spin 1.1s linear infinite;
                }

                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
            </style>

            <div class="wrapper-loader">
                <div class="loader"></div>
            </div>

            <script>window.history.replaceState( "twispay", "Twispay", "../twispay.php" );</script>';

        /* Require the "Twispay_TW_Helper_Notify" class. */
        require_once( TWISPAY_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'Twispay_TW_Helper_Notify.php' );


        /* Load languages. */
        $lang = explode( '-', get_bloginfo( 'language' ) )[0];
        if ( file_exists( TWISPAY_PLUGIN_DIR . 'lang/' . $lang . '/lang.php' ) ) {
            require( TWISPAY_PLUGIN_DIR . 'lang/' . $lang . '/lang.php' );
        } else {
            require( TWISPAY_PLUGIN_DIR . 'lang/en/lang.php' );
        }


        /* Exit if no order is placed */
        if ( isset( $_GET['order_id'] ) && sanitize_key( $_GET['order_id'] ) ) {
            /* Extract the WooCommerce order. */
            $order = wc_get_order((int) sanitize_key( $_GET['order_id'] ));

            if ( FALSE != $order ) {
                /* Get all information for the Twispay Payment form. */
                $data = $order->get_data();

                /* Get configuration from database. */
                global $wpdb;
                $configuration = $wpdb->get_row( "SELECT * FROM " . $wpdb->prefix . "twispay_tw_configuration" );

                /* Get the Site ID and the Private Key. */
                $siteID = '';
                $secretKey = '';
                if ( $configuration ) {
                    if ( 1 == $configuration->live_mode ) {
                        $siteID = $configuration->live_id;
                        $secretKey = $configuration->live_key;
                    } else if ( 0 == $configuration->live_mode ) {
                        $siteID = $configuration->staging_id;
                        $secretKey = $configuration->staging_key;
                    } else {
                        echo '<style>.loader {display: none;}</style>';
                        die( esc_html( $tw_lang['twispay_processor_error_missing_configuration'] ) );
                    }
                }

                /** Save the timestamp of this payment. */
                $timestamp = date('YmdHis');

                /* Extract the customer details. */
                $customer = [ 'identifier' => 'p_wo_' . ((0 == $data['customer_id']) ? (sanitize_key( $_GET['order_id'] )) : ($data['customer_id'])) . '_' . $timestamp
                    , 'firstName' => ($data['billing']['first_name']) ? ($data['billing']['first_name']) : ($data['shipping']['first_name'])
                    , 'lastName' => ($data['billing']['last_name']) ? ($data['billing']['last_name']) : ($data['shipping']['last_name'])
                    , 'country' => ($data['billing']['country']) ? ($data['billing']['country']) : ($data['shipping']['country'])
                    /* , 'state' => ($data['billing']['state']) ? ($data['billing']['country']) : ($data['shipping']['country']) */
                    , 'city' => ($data['billing']['city']) ? ($data['billing']['city']) : ($data['shipping']['city'])
                    , 'address' => ($data['billing']['address_1']) ? ($data['billing']['address_1']/* . ' ' . $data['billing']['address_2']*/) : ($data['shipping']['address_1']/* . ' ' . $data['shipping']['address_2']*/)
                    , 'zipCode' => ($data['billing']['postcode']) ? ($data['billing']['postcode']) : ($data['shipping']['postcode'])
                    , 'phone' => (('+' == $data['billing']['phone'][0]) ? ('+') : ('')) . preg_replace('/([^0-9]*)+/', '', $data['billing']['phone'])
                    , 'email' => $data['billing']['email']
                    /* , 'tags' => [] */
                ];

                /* Extract the items details. */
                $items = array();
                foreach ( $order->get_items() as $item ) {
                    $items[] = [ 'item' => $item['name']
                        , 'units' =>  $item['quantity']
                        , 'unitPrice' => number_format( number_format( ( float )$item['subtotal'], 2) / number_format( ( float )$item['quantity'], 2 ), 2 )
                        /* , 'type' => '' */
                        /* , 'code' => '' */
                        /* , 'vatPercent' => '' */
                        /* , 'itemDescription' => '' */
                    ];
                }

                /* Calculate the backUrl through which the server will pvide the status of the order. */
                $backUrl = get_permalink( get_page_by_path( 'twispay-confirmation' ) );
                $backUrl .= (FALSE == strpos($backUrl, '?')) ? ('?secure_key=' . $data['cart_hash']) : ('&secure_key=' . $data['cart_hash']);

                /* Build the data object to be posted to Twispay. */
                $orderData = [ 'siteId' => $siteID
                    , 'customer' => $customer
                    , 'order' => [ 'orderId' => sanitize_key( $_GET['order_id'] ) . '_' . $timestamp
                        , 'type' => 'purchase'
                        , 'amount' => $data['total']
                        , 'currency' => $data['currency']
                        , 'items' => $items
                        /* , 'tags' => [] */
                        /* , 'intervalType' => '' */
                        /* , 'intervalValue' => 1 */
                        /* , 'trialAmount' => 1 */
                        /* , 'firstBillDate' => '' */
                        /* , 'level3Type' => '', */
                        /* , 'level3Airline' => [ 'ticketNumber' => '' */
                        /*                      , 'passengerName' => '' */
                        /*                      , 'flightNumber' => '' */
                        /*                      , 'departureDate' => '' */
                        /*                      , 'departureAirportCode' => '' */
                        /*                      , 'arrivalAirportCode' => '' */
                        /*                      , 'carrierCode' => '' */
                        /*                      , 'travelAgencyCode' => '' */
                        /*                      , 'travelAgencyName' => ''] */
                    ]
                    , 'cardTransactionMode' => 'authAndCapture'
                    /* , 'cardId' => 0 */
                    , 'invoiceEmail' => ''
                    , 'backUrl' => $backUrl
                    /* , 'customData' => [] */
                ];

                /* Build the HTML form to be posted to Twispay. */
                $base64JsonRequest = Twispay_TW_Helper_Notify::getBase64JsonRequest($orderData);
                $base64Checksum = Twispay_TW_Helper_Notify::getBase64Checksum($orderData, $secretKey);
                $hostName = ($configuration && (1 == $configuration->live_mode)) ? ('https://secure.twispay.com' . '?lang=' . $lang) : ('https://secure-stage.twispay.com' . '?lang=' . $lang);


                $str .= '<form action="' . esc_attr( $hostName ) . '" method="POST" accept-charset="UTF-8" id="twispay_payment_form">
                            <input type="hidden" name="jsonRequest" value="' . esc_attr( $base64JsonRequest ) . '">
                            <input type="hidden" name="checksum" value="' . esc_attr( $base64Checksum ) . '">
                        </form>
                        <script>document.getElementById( "twispay_payment_form" ).submit();</script>';

                echo $str;
            } else {
                $str .= '<style>.loader {display: none;}</style>';
                echo $str;
                die( esc_html( $tw_lang['twispay_processor_error_general'] ) );
            }
        } else {
            $str .= '<style>.loader {display: none;}</style>';
            echo $str;
            die( esc_html( $tw_lang['twispay_processor_error_general'] ) );
        }
    }

    public function twispay_subscription_processor() {
        $str = '<style>
                body {
                    height: 100%;
                    overflow: hidden !important;
                }
                .wrapper-loader{
                    background-color: #fff;
                    height: 100%;
                    left: 0;
                    position: absolute;
                    width: 100%;
                    top: 0;
                    z-index: 1000;
                }
                .loader {
                    margin: 15% auto 0;
                    border: 14px solid #f3f3f3;
                    border-top: 14px solid #3498db;
                    border-radius: 50%;
                    width: 110px;
                    height: 110px;
                    animation: spin 1.1s linear infinite;
                }
        
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
            </style>
        
            <div class="wrapper-loader">
                <div class="loader"></div>
            </div>
        
            <script>window.history.replaceState( "twispay", "Twispay", "../twispay.php" );</script>';

        /* Require the "Twispay_TW_Helper_Notify" class. */
        require_once( TWISPAY_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'Twispay_TW_Helper_Notify.php' );


        /* Load languages. */
        $lang = explode( '-', get_bloginfo( 'language' ) )[0];
        if ( file_exists( TWISPAY_PLUGIN_DIR . 'lang/' . $lang . '/lang.php' ) ) {
            require( TWISPAY_PLUGIN_DIR . 'lang/' . $lang . '/lang.php' );
        } else {
           require( TWISPAY_PLUGIN_DIR . 'lang/en/lang.php' );
        }


        /* Exit if no order is placed */
        if ( isset( $_GET['order_id'] ) && sanitize_key( $_GET['order_id'] ) ) {
            /* Extract the WooCommerce order. */
            $order_id = (int) sanitize_key( $_GET['order_id'] );
            $order = wc_get_order($order_id);

            if (FALSE != $order && (TRUE == wcs_order_contains_subscription($order_id)) && (1 == count($order->get_items()))) {
                $subscription = wcs_get_subscriptions_for_order($order);
                $subscription = reset($subscription);
                /* Get all information for the Twispay Payment form. */
                $data = $subscription->get_data();

                /* Get configuration from database. */
                global $wpdb;
                $table_name = $wpdb->prefix . 'twispay_tw_configuration';

                $configuration = $wpdb->get_row("SELECT * FROM $table_name" );

                /* Get the Site ID and the Private Key. */
                $siteID = '';
                $secretKey = '';
                if ( $configuration ) {
                    if ( 1 == $configuration->live_mode ) {
                        $siteID = $configuration->live_id;
                        $secretKey = $configuration->live_key;
                    } else if ( 0 == $configuration->live_mode ) {
                        $siteID = $configuration->staging_id;
                        $secretKey = $configuration->staging_key;
                    } else {
                        echo '<style>.loader {display: none;}</style>';
                        die( esc_html( $tw_lang['twispay_processor_error_missing_configuration'] ) );
                    }
                }

                /** Save the timestamp of this payment. */
                $timestamp = date('YmdHis');

                /* Extract the customer details. */
                $customer = [ 'identifier' => 'r_wo_' . ((0 == $data['customer_id']) ? ($order_id) : ($data['customer_id'])) . '_' . $timestamp
                            , 'firstName' => ($data['billing']['first_name']) ? ($data['billing']['first_name']) : ($data['shipping']['first_name'])
                            , 'lastName' => ($data['billing']['last_name']) ? ($data['billing']['last_name']) : ($data['shipping']['last_name'])
                            , 'country' => ($data['billing']['country']) ? ($data['billing']['country']) : ($data['shipping']['country'])
                            /* , 'state' => ($data['billing']['state']) ? ($data['billing']['country']) : ($data['shipping']['country']) */
                            , 'city' => ($data['billing']['city']) ? ($data['billing']['city']) : ($data['shipping']['city'])
                            , 'address' => ($data['billing']['address_1']) ? ($data['billing']['address_1']/* . ' ' . $data['billing']['address_2']*/) : ($data['shipping']['address_1']/* . ' ' . $data['shipping']['address_2']*/)
                            , 'zipCode' => ($data['billing']['postcode']) ? ($data['billing']['postcode']) : ($data['shipping']['postcode'])
                            , 'phone' => $data['billing']['phone']
                            , 'email' => $data['billing']['email']
                            /* , 'tags' => [] */
                            ];

                /* Extract the item details. */
                $item = $subscription->get_items();
                $item = reset($item);

                /* Calculate the backUrl through which the server will provide the status of the initial payment. */
                $backUrl = get_permalink( get_page_by_path( 'twispay-confirmation' ) );
                $backUrl .= (FALSE == strpos($backUrl, '?')) ? ('?secure_key=' . $order->get_data()['cart_hash']) : ('&secure_key=' . $order->get_data()['cart_hash']);

                /* !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! */
                /* !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! IMPORTANT !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! */
                /* READ:  We presume that there will be ONLY ONE subscription product inside the order. */
                /* !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! */
                /* !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! */

                /* Extract the subscription details. */
                $trialAmount = WC_Subscriptions_Product::get_sign_up_fee($item['product_id']);
                $firstBillDate = explode(' ', WC_Subscriptions_Product::get_trial_expiration_date($item['product_id']))[0];

                /* Calculate the subscription's interval type and value. */
                $intervalType = $subscription->get_billing_period();
                $intervalValue = $subscription->get_billing_interval();
                switch ($intervalType) {
                    case 'week':
                        /* Convert weeks to days. */
                        $intervalType = 'day';
                        $intervalValue = /*days/week*/7 * $intervalValue;
                        break;
                    case 'year':
                        /* Convert years to months. */
                        $intervalType = 'month';
                        $intervalValue = /*months/year*/12 * $intervalValue;
                        break;
                    default:
                        /* We change nothing in case of DAYS and MONTHS */
                        break;
                }

                /* Build the data object to be posted to Twispay. */
                $orderData = [ 'siteId' => $siteID
                             , 'customer' => $customer
                             , 'order' => [ 'orderId' => (int) sanitize_key( $_GET['order_id'] ) . '_' . $timestamp
                                          , 'type' => 'recurring'
                                          , 'amount' => $data['total'] /* Total sum to pay right now. */
                                          , 'currency' => $data['currency']
                                          ]
                             , 'cardTransactionMode' => 'authAndCapture'
                             , 'invoiceEmail' => ''
                             , 'backUrl' => $backUrl
                ];

                /* Add the subscription data. */
                $orderData['order']['intervalType'] = $intervalType;
                $orderData['order']['intervalValue'] = $intervalValue;
                if('0' != $trialAmount){
                    $orderData['order']['trialAmount'] = $trialAmount;
                    $orderData['order']['firstBillDate'] = $firstBillDate;
                }
                $orderData['order']['description'] = $intervalValue . " " . $intervalType . " subscription " . $item['name'];

                /* Build the HTML form to be posted to Twispay. */
                $base64JsonRequest = Twispay_TW_Helper_Notify::getBase64JsonRequest($orderData);
                $base64Checksum = Twispay_TW_Helper_Notify::getBase64Checksum($orderData, $secretKey);
                $hostName = ($configuration && (1 == $configuration->live_mode)) ? ('https://secure.twispay.com' . '?lang=' . $lang) : ('https://secure-stage.twispay.com' . '?lang=' . $lang);

                $str .= '<form action="' . esc_attr( $hostName ) . '" method="POST" accept-charset="UTF-8" id="twispay_payment_form">
                            <input type="hidden" name="jsonRequest" value="' . esc_attr( $base64JsonRequest ) . '">
                            <input type="hidden" name="checksum" value="' . esc_attr( $base64Checksum ) . '">
                        </form>
                        <script>document.getElementById( "twispay_payment_form" ).submit();</script>';

                echo $str;

            } else {
                if(FALSE == $order){
                    $str .= '<style>.loader {display: none;}</style>';
                    echo $str;
                    die( esc_html( $tw_lang['twispay_processor_error_general'] ) );
                } else if(1 < count($order->get_items())){
                    $str .= '<style>.loader {display: none;}</style>';
                    echo $str;
                    die( esc_html( $tw_lang['twispay_processor_error_more_items'] ) );
                } else {
                    $str .= '<style>.loader {display: none;}</style>';
                    echo $str;
                    die( esc_html( $tw_lang['twispay_processor_error_no_item'] ) );
                }
            }
        } else {
            $str .= '<style>.loader {display: none;}</style>';
            echo $str;
            die( esc_html( $tw_lang['twispay_processor_error_general'] ) );
        }
    }

    public function server_to_server() {

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
            die( esc_html( $tw_lang['log_error_invalid_private'] ));
        }

        /* Extract the server response and decript it. */
        $decrypted = Twispay_TW_Helper_Response::twispay_tw_decrypt_message(/*tw_encryptedResponse*/(isset($_POST['opensslResult'])) ? (esc_html($_POST['opensslResult'])) : (esc_html($_POST['result'])), $secretKey, $tw_lang);


        /* Check if decryption failed.  */
        if(FALSE === $decrypted){
            Twispay_TW_Logger::twispay_tw_log($tw_lang['log_error_decryption_error']);
            Twispay_TW_Logger::twispay_tw_log($tw_lang['log_error_openssl'] . (isset($_POST['opensslResult'])) ? (esc_html($_POST['opensslResult'])) : (esc_html($_POST['result'])));
            die( esc_html( $tw_lang['log_error_decryption_error'] ));
        } else {
            Twispay_TW_Logger::twispay_tw_log($tw_lang['log_ok_string_decrypted']);
        }


        /* Validate the decripted response. */
        $orderValidation = Twispay_TW_Helper_Response::twispay_tw_checkValidation($decrypted, $tw_lang);


        /* Check if server sesponse validation failed.  */
        if(TRUE !== $orderValidation){
            Twispay_TW_Logger::twispay_tw_log($tw_lang['log_error_validating_failed']);
            die( esc_html( $tw_lang['log_error_validating_failed'] ));
        }


        /* Extract the WooCommerce order. */
        $orderId = (int) explode('_', $decrypted['externalOrderId'])[0];
        $order = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "posts WHERE ID='%d'", $orderId) );


        /* Check if the WooCommerce order extraction failed. */
        if( FALSE == $order ){
            Twispay_TW_Logger::twispay_tw_log($tw_lang['log_error_invalid_order']);
            die( esc_html( $tw_lang['log_error_invalid_order'] ));
        }


        /* Extract the transaction status. */
        $status = (empty($decrypted['status'])) ? ($decrypted['transactionStatus']) : ($decrypted['status']);


        /* Set the status of the WooCommerce order according to the received status. */
        Twispay_TW_Status_Updater::updateStatus_IPN($orderId, $status, $tw_lang);

        /* Send the 200 OK response back to the Twispay server. */
        die('OK');
    }
}

endif; // End if class_exists

function twispay_missing_wc_notice() {
  $lang = explode( '-', get_bloginfo( 'language' ) );
  $lang = $lang[0];

  if ( file_exists( plugin_dir_path( __FILE__ ) . 'lang/' . $lang . '/lang.php' ) ) {
    require( plugin_dir_path( __FILE__ ) . 'lang/' . $lang . '/lang.php' );
  } else {
    require( plugin_dir_path( __FILE__ ) . 'lang/en/lang.php' );
  }
  ?>

  <div class="error notice" style="margin-top: 20px;">
    <p><?= esc_html( $tw_lang['no_woocommerce_f'] ); ?> <a target="_blank" href="https://wordpress.org/plugins/woocommerce/"><?= esc_html( $tw_lang['no_woocommerce_s'] ); ?></a>.</p>
    <div class="clearfix"></div>
  </div>

  <?php
}

/**
 * The main instance of Twispay
 *
 * This function is used like a global variable, but without to
 * declare the global
 *
 * @return Twispay|false
 */
function TW() {
  /*
  The way I check if WC is active is a little hacky, but at least it works.

  I've tried to call this function using actions, but the payment method is missed both
  in admin and checkout page in that case.
  */
  if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    add_action('admin_notices', 'twispay_missing_wc_notice');
    return false;
  }

  return Twispay::instance();
}

TW();

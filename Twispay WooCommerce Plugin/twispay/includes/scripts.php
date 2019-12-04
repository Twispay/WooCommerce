<?php
/**
 * Twispay Scripts Page
 *
 * Add the js and css files for administrator pages and for non-administrator pages
 *
 * @package  Twispay/Admin
 * @category Admin
 * @author   Twispay
 * @version  1.0.8
 */

/* Require the "Twispay_TW_Logger" class. */
require_once( TWISPAY_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'Twispay_TW_Logger.php' );

/**
 * Twispay Admin Checker
 *
 * Check if the current page is an Twispay Admin Page or not
 *
 * @public
 * @return bool True if is an admin page, false otherwise
 */
function twispay_tw_check_if_is_admin() {
    // Check if is admin page
    if ( ! is_admin() ) {
        return false;
    }

    // Check if the page parameters is present
    if ( ! isset( $_GET['page'] ) ) {
        return false;
    }

    // Make array with all Twispay Pages
    $tw_pages = array(
        'twispay',
        'tw-transaction'
    );

    // Check if current page is one of the Twispay Pages
    return in_array( $_GET['page'], $tw_pages );
}


/**
 * Twispay Add Admin Js
 *
 * This function will add all js script ONLY for Twispay Pages
 *
 * @public
 * @return void
 */
function twispay_tw_add_admin_js() {
    // Check if current page is an Twispay Admin Page
    if ( ! twispay_tw_check_if_is_admin() ) {
        return;
    }

    // Load all admin js files for Administrator Pages
    wp_enqueue_script( 'ma-admin', plugins_url( '../assets/js/admin.js', __FILE__ ) );
    //wp_enqueue_script( 'ma-admin-jquery', plugins_url( '../assets/js/jquery-ui.min.js', __FILE__ ) );
}
add_action( 'admin_enqueue_scripts', 'twispay_tw_add_admin_js' );


/**
 * Twispay Add Admin Css
 *
 * This function will add all css files ONLY for Twispay Pages
 *
 * @public
 * @return void
 */
function twispay_tw_add_admin_css() {
    // Check if current page is an Twispay Admin Page
    if ( ! twispay_tw_check_if_is_admin() ) {
        return;
    }

    // Load all admin css files for Administrator Pages
    wp_enqueue_style( 'ma-admin', plugins_url( '../assets/css/admin.css', __FILE__ ) );
}
add_action( 'admin_enqueue_scripts', 'twispay_tw_add_admin_css' );


/**
 * Twispay Add Front Css
 *
 * This function will add all front css files
 *
 * @public
 * @return void
 */
function twispay_tw_add_front_css() {
    // Load all front css files
    wp_enqueue_style( 'ma-front', plugins_url( '../assets/css/front.css', __FILE__ ) );
}
add_action( 'wp_enqueue_scripts', 'twispay_tw_add_front_css' );


/**
 * Twispay init the Payment Gateway
 *
 * This function will load the payment gateway class
 *
 * @public
 * @return void
 */
function init_twispay_gateway_class() {
    if ( class_exists( 'WooCommerce' ) ) {
        class WC_Gateway_Twispay_Gateway extends WC_Payment_Gateway {
            /**
             * Twispay Gateway Constructor
             *
             * @public
             * @return void
             */
            public function __construct() {
                /* Load languages */
                $lang = explode( '-', get_bloginfo( 'language' ) )[0];
                if ( file_exists( TWISPAY_PLUGIN_DIR . 'lang/' . $lang . '/lang.php' ) ) {
                    require( TWISPAY_PLUGIN_DIR . 'lang/' . $lang . '/lang.php' );
                } else {
                    require( TWISPAY_PLUGIN_DIR . 'lang/en/lang.php' );
                }

                $this->id = 'twispay';
                $this->icon =  plugins_url() . '/twispay/logo.png';
                $this->has_fields = true;
                $this->method_title = $tw_lang['ws_title'];
                $this->method_description = $tw_lang['ws_description'];
                if( class_exists('WC_Subscriptions') ){
                    $this->supports = [ 'products'
                                      , 'refunds'
                                      , 'subscriptions'
                                      , 'subscription_cancellation'
                                      , 'subscription_suspension'
                                      , 'subscription_reactivation'
                                      , 'subscription_amount_changes'
                                      , 'subscription_date_changes'
                                      , 'subscription_payment_method_change'
                                      , 'subscription_payment_method_change_customer'
                                      , 'subscription_payment_method_change_admin'
                                      , 'multiple_subscriptions'
                                      , 'gateway_scheduled_payments'];
                } else {
                    $this->supports = [ 'products', 'refunds' ];
                }

                $this->init_form_fields();
                $this->init_settings();

                $this->title = $this->get_option( 'title' );
                $this->description = $this->get_option( 'description' );
                $this->enable_for_methods = $this->get_option( 'enable_for_methods', array() );
                $this->enable_for_virtual = $this->get_option( 'enable_for_virtual', 'yes' ) === 'yes' ? true : false;

                $shipping_methods = array();

                foreach ( WC()->shipping()->load_shipping_methods() as $method ) {
                    $shipping_methods[ $method->id ] = $method->get_method_title();
                }

                $this->form_fields = array(
                    'enabled' => array(
                        'title'    => __( $tw_lang['ws_enable_disable_title'], 'woocommerce' ),
                        'type'     => 'checkbox',
                        'label'    => __( $tw_lang['ws_enable_disable_label'], 'woocommerce' ),
                        'default'  => 'yes'
                    ),
                    'title' => array(
                        'title'        => __( $tw_lang['ws_title_title'], 'woocommerce' ),
                        'type'         => 'text',
                        'description'  => __( $tw_lang['ws_title_desc'], 'woocommerce' ),
                        'default'      => __( 'Twispay', 'woocommerce' ),
                        'desc_tip'     => true
                    ),
                    'description' => array(
                        'title'        => __( $tw_lang['ws_description_title'], 'woocommerce' ),
                        'type'         => 'textarea',
                        'description'  => __( $tw_lang['ws_description_desc'], 'woocommerce' ),
                        'default'      => __( $tw_lang['ws_description_default'], 'woocommerce' ),
                        'desc_tip'     => true
                    ),
                    'enable_for_methods' => array(
                        'title'              => __( $tw_lang['ws_enable_methods_title'], 'woocommerce' ),
                        'type'               => 'multiselect',
                        'class'              => 'wc-enhanced-select',
                        'css'                => 'width: 400px;',
                        'default'            => '',
                        'description'        => __( $tw_lang['ws_enable_methods_desc'], 'woocommerce' ),
                        'options'            => $shipping_methods,
                        'desc_tip'           => true,
                        'custom_attributes'  => array(
                            'data-placeholder'  => __( $tw_lang['ws_enable_methods_placeholder'], 'woocommerce' ),
                        ),
                    ),
                    'enable_for_virtual' => array(
                        'title'    => __( $tw_lang['ws_vorder_title'], 'woocommerce' ),
                        'label'    => __( $tw_lang['ws_vorder_desc'], 'woocommerce' ),
                        'type'     => 'checkbox',
                        'default'  => 'yes',
                    )
                );

                add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
            }

            /**
            * Check if the Twispay Gateway is available for use
            *
            * @return bool
            */
            public function is_available() {
                $order          = null;
                $needs_shipping = false;

                // Test if shipping is needed first
                if ( WC()->cart && WC()->cart->needs_shipping() ) {
                    $needs_shipping = true;
                }
                elseif ( is_page( wc_get_page_id( 'checkout' ) ) && 0 < get_query_var( 'order-pay' ) ) {
                    $order_id = absint( get_query_var( 'order-pay' ) );
                    $order    = wc_get_order( $order_id );

                    // Test if order needs shipping.
                    if ( 0 < sizeof( $order->get_items() ) ) {
                        foreach ( $order->get_items() as $item ) {
                            $_product = $item->get_product();
                            if ( $_product && $_product->needs_shipping() ) {
                                $needs_shipping = true;
                                break;
                            }
                        }
                    }
                }

                $needs_shipping = apply_filters( 'woocommerce_cart_needs_shipping', $needs_shipping );

                // Virtual order, with virtual disabled
                if ( ! $this->enable_for_virtual && ! $needs_shipping ) {
                    return false;
                }

                // Check methods
                if ( ! empty( $this->enable_for_methods ) && $needs_shipping ) {
                    // Only apply if all packages are being shipped via chosen methods, or order is virtual
                    $chosen_shipping_methods_session = WC()->session->get( 'chosen_shipping_methods' );

                    if ( isset( $chosen_shipping_methods_session ) ) {
                        $chosen_shipping_methods = array_unique( $chosen_shipping_methods_session );
                    }
                    else {
                        $chosen_shipping_methods = array();
                    }

                    $check_method = false;

                    if ( is_object( $order ) ) {
                        if ( $order->shipping_method ) {
                            $check_method = $order->shipping_method;
                        }
                    }
                    elseif ( empty( $chosen_shipping_methods ) || sizeof( $chosen_shipping_methods ) > 1 ) {
                        $check_method = false;
                    }
                    elseif ( sizeof( $chosen_shipping_methods ) == 1 ) {
                        $check_method = $chosen_shipping_methods[0];
                    }

                    if ( ! $check_method ) {
                        return false;
                    }

                    if ( strstr( $check_method, ':' ) ) {
                        $check_method = current( explode( ':', $check_method ) );
                    }

                    $found = false;

                    foreach ( $this->enable_for_methods as $method_id ) {
                        if ( $check_method === $method_id ) {
                            $found = true;
                            break;
                        }
                    }

                    if ( ! $found ) {
                        return false;
                    }
                }

                return parent::is_available();
            }

            /**
             * Twispay Process Payment function
             *
             * @public
             * @return array with Result and Redirect
             */
            function process_payment( $order_id ) {
                /* Extract the order; */
                $order = new WC_Order( $order_id );

                /* Check if the order contains a subscription. */
                if(class_exists('WC_Subscriptions') && (TRUE == wcs_order_contains_subscription($order_id))){
                    /* Redirect to file that processes the subscriptions payments requests. */
                    return array('result' => 'success', 'redirect' => plugin_dir_url( __FILE__ ) . 'twispay-subscription-processor.php?order_id=' . $order_id);
                } else {
                    /* Redirect to file that processes the purchase payments requests. */
                    return array('result' => 'success', 'redirect' => plugin_dir_url( __FILE__ ) . 'twispay-processor.php?order_id=' . $order_id);
                }
            }


            /**
             * Twispay Process Payment function
             *
             * @param order_id: The ID of the order witha. the refund.
             * @param amount: The amount to be refunded.
             * @param reason: The reason of the refund.
             *
             * @return boolean: True or false based on success
             */
            function process_refund($order_id, $amount = NULL, $reason = '') {
                global $wpdb;
                $apiKey = '';
                $transaction_id = $wpdb->get_var( "SELECT transactionId FROM " . $wpdb->prefix . "twispay_tw_transactions WHERE id_cart = '" . $order_id . "'" );

                /* Get configuration from database. */
                $configuration = $wpdb->get_row( "SELECT * FROM " . $wpdb->prefix . "twispay_tw_configuration" );

                if ( $configuration ) {
                    if ( 1 == $configuration->live_mode ) {
                        $apiKey = $configuration->live_key;
                        $url = 'https://api.twispay.com/transaction/' . $transaction_id;
                    } else if ( 0 == $configuration->live_mode ) {
                        $apiKey = $configuration->staging_key;
                        $url = 'https://api-stage.twispay.com/transaction/' . $transaction_id;
                    }
                }

                $args = array('method' => 'DELETE', 'headers' => ['accept' => 'application/json', 'Authorization' => $apiKey]);
                $response = wp_remote_request( $url, $args );

                if ( 'OK' == $response['response']['message'] ) {
                    Twispay_TW_Logger::twispay_tw_updateTransactionStatus($order_id, Twispay_TW_Status_Updater::$RESULT_STATUSES['REFUND_OK']);
                    return TRUE;
                } else {
                    return FALSE;
                }
            }
        }
    }
}
add_action( 'plugins_loaded', 'init_twispay_gateway_class' );


/**
 * Add the Twispay gateway class
 *
 * @public
 * @return array $methods
 */
function add_twispay_gateway_class( $methods ) {
    if ( class_exists( 'WooCommerce' ) ) {
        $methods[] = 'WC_Gateway_Twispay_Gateway';
        return $methods;
    }
}
add_filter( 'woocommerce_payment_gateways', 'add_twispay_gateway_class' );


/**
 * Twispay Prepare buffer functions
 *
 * This function will prepare the buffer in order to use wp_redirect properly
 *
 * @public
 * @return void
 */
function twispay_tw_start_buffer_output() {
    ob_start();
}
add_action('init', 'twispay_tw_start_buffer_output');


/**
 * Custom text on the receipt page.
 */
function twispay_tw_isa_order_received_text( $text, $order ) {
    // Load languages
    $lang = explode( '-', get_bloginfo( 'language' ) );
    $lang = $lang[0];
    if ( file_exists( TWISPAY_PLUGIN_DIR . 'lang/' . $lang . '/lang.php' ) ) {
        require( TWISPAY_PLUGIN_DIR . 'lang/' . $lang . '/lang.php' );
    } else {
        require( TWISPAY_PLUGIN_DIR . 'lang/en/lang.php' );
    }

    return $tw_lang['order_confirmation_title'];
}
add_filter('woocommerce_thankyou_order_received_text', 'twispay_tw_isa_order_received_text', 10, 2 );


/**
 * Suppress email functionality
 */
function twispay_tw_unhook_woo_order_emails( $email_class ) {
    // New order emails
    remove_action( 'woocommerce_order_status_pending_to_processing_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
    remove_action( 'woocommerce_order_status_pending_to_completed_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
    remove_action( 'woocommerce_order_status_pending_to_on-hold_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
    remove_action( 'woocommerce_order_status_failed_to_processing_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
    remove_action( 'woocommerce_order_status_failed_to_completed_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
    remove_action( 'woocommerce_order_status_failed_to_on-hold_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );

    // Processing order emails
    remove_action( 'woocommerce_order_status_pending_to_processing_notification', array( $email_class->emails['WC_Email_Customer_Processing_Order'], 'trigger' ) );
    remove_action( 'woocommerce_order_status_pending_to_on-hold_notification', array( $email_class->emails['WC_Email_Customer_Processing_Order'], 'trigger' ) );

    // Completed order emails
    remove_action( 'woocommerce_order_status_completed_notification', array( $email_class->emails['WC_Email_Customer_Completed_Order'], 'trigger' ) );
}

// Get configuration from database
global $wpdb;
$suppress_email = $wpdb->get_row( "SELECT suppress_email FROM " . $wpdb->prefix . "twispay_tw_configuration" );

if ( $suppress_email ) {
    if ( $suppress_email->suppress_email == 1 ) {
        add_action( 'woocommerce_email', 'twispay_tw_unhook_woo_order_emails' );
    }
}



function subscription_terminated( $subscription ){
    /* Get configuration from database. */
    global $wpdb;
    $apiKey = '';
    $configuration = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "twispay_tw_configuration");
    $serverOrderId = $wpdb->get_var( "SELECT orderId FROM " . $wpdb->prefix . "twispay_tw_transactions WHERE id_cart = '" . $subscription->get_parent_id() . "'" );
    if ( $configuration ) {
        if ( $configuration->live_mode == 1 ) {
            $apiKey = $configuration->live_key;
            $url = 'https://api.twispay.com/order/' . $serverOrderId;
        } else if ( $configuration->live_mode == 0 ) {
            $apiKey = $configuration->staging_key;
            $url = 'https://api-stage.twispay.com/order/' . $serverOrderId;
        }
    }

    /* Load languages */
    $lang = explode( '-', get_bloginfo( 'language' ) )[0];
    if ( file_exists( TWISPAY_PLUGIN_DIR . 'lang/' . $lang . '/lang.php' ) ) {
        require( TWISPAY_PLUGIN_DIR . 'lang/' . $lang . '/lang.php' );
    } else {
        require( TWISPAY_PLUGIN_DIR . 'lang/en/lang.php' );
    }

    $args = array( 'method' => 'DELETE'
                  , 'headers' => ['accept' => 'application/json', 'Authorization' => $apiKey]);
    $response = wp_remote_request($url, $args);

    if ( $response['response']['message'] == 'OK' ) {
        Twispay_TW_Logger::twispay_tw_log($tw_lang['subscriptions_log_ok_set_status'] . $subscription->get_parent_id());
    } else {
        Twispay_TW_Logger::twispay_tw_log($tw_lang['subscriptions_log_error_set_status'] . $subscription->get_parent_id());
    }
}
add_action('woocommerce_subscription_status_cancelled', 'subscription_terminated');
add_action('woocommerce_subscription_status_expired', 'subscription_terminated');

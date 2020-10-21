<?php
/**
 * Twispay Custom Processor Page
 *
 * Here the Twispay data for a subscription is extracted and sent to the gateway.
 *
 * @package  Twispay/Admin
 * @category Admin
 * @author   Twispay
 * @version  1.0.8
 */
?>


<style>
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

<div class="loader"></div>

<script>window.history.replaceState( 'twispay', 'Twispay', '../twispay.php' );</script>


<?php
$parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );
require_once( $parse_uri[0] . 'wp-load.php' );

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
if ( isset( $_GET['order_id'] ) && $_GET['order_id'] ) {
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

        $configuration = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name" ) );

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
        ?>

            <form action="<?= esc_attr( $hostName ); ?>" method="POST" accept-charset="UTF-8" id="twispay_payment_form">
                <input type="hidden" name="jsonRequest" value="<?= esc_attr( $base64JsonRequest ); ?>">
                <input type="hidden" name="checksum" value="<?= esc_html( $base64Checksum ); ?>">
            </form>

            <script>document.getElementById( 'twispay_payment_form' ).submit();</script>

        <?php
    } else {
        if(FALSE == $order){
            echo '<style>.loader {display: none;}</style>';
            die( esc_html( $tw_lang['twispay_processor_error_general'] ) );
        } else if(1 < count($order->get_items())){
            echo '<style>.loader {display: none;}</style>';
            die( esc_html( $tw_lang['twispay_processor_error_more_items'] ) );
        } else {
            echo '<style>.loader {display: none;}</style>';
            die( esc_html( $tw_lang['twispay_processor_error_no_item'] ) );
        }
    }
} else {
    echo '<style>.loader {display: none;}</style>';
    die( esc_html( $tw_lang['twispay_processor_error_general'] ) );
}

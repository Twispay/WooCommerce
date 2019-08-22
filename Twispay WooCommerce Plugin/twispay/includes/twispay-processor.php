<?php
/**
 * Twispay Custom Processor Page
 *
 * Here the Twispay Form is created and processed to the gateway
 *
 * @package  Twispay/Admin
 * @category Admin
 * @author   twispay
 * @version  1.0.1
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
    $order = wc_get_order($_GET['order_id']);

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
                /* TODO: Error? */
            }
        }

        /* Extract the customer details. */
        $customer = [ 'identifier' => (0 == $data['customer_id']) ? ('_' . $_GET['order_id'] . '_' . date('YmdHis')) : ('_' . $data['customer_id'] . '_' . date('YmdHis'))
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

        error_log("customer=" . print_r($customer), true);

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
                     , 'order' => [ 'orderId' => (isset( $_GET['tw_reload'] ) && $_GET['tw_reload']) ? ($_GET['order_id'] . '_' . date('YmdHis')) : ($_GET['order_id'])
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
        ?>

            <form action="<?= $hostName; ?>" method="POST" accept-charset="UTF-8" id="twispay_payment_form">
                <input type="hidden" name="jsonRequest" value="<?= $base64JsonRequest; ?>">
                <input type="hidden" name="checksum" value="<?= $base64Checksum; ?>">
            </form>

            <script>document.getElementById( 'twispay_payment_form' ).submit();</script>

        <?php
    } else {
        echo '<style>.loader {display: none;}</style>';
        die( $tw_lang['twispay_processor_error_general'] );
    }
} else {
    echo '<style>.loader {display: none;}</style>';
    die( $tw_lang['twispay_processor_error_general'] );
}

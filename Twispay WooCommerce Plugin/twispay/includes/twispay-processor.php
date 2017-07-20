<?php
/**
 * Twispay Custom Processor Page
 *
 * Here the Twispay Form is created and processed to the gateway
 *
 * @package  Twispay/Admin
 * @category Admin
 * @author   @TODO
 * @version  0.0.1
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

// Load languages
$lang = explode( '-', get_bloginfo( 'language' ) );
$lang = $lang[0];
if ( file_exists( TWISPAY_PLUGIN_DIR . 'lang/' . $lang . '/lang.php' ) ) {
    require( TWISPAY_PLUGIN_DIR . 'lang/' . $lang . '/lang.php' );
} else {
    require( TWISPAY_PLUGIN_DIR . 'lang/en/lang.php' );
}
 
// Exit if no order is placed
if ( isset( $_GET['order_id'] ) && $_GET['order_id'] ) {
    global $woocommerce;
    $order = '';
    
    try {
        $order = new WC_Order( $_GET['order_id'] );
    }
    catch( Exception $e ) {
        
    }
    
    if ( $order ) {
        // Get all information for the Twispay Payment Form
        $inputs = array();
        $data = $order->get_data();
        
        // Get configuration from database
        global $wpdb;
        $configuration = $wpdb->get_row( "SELECT * FROM " . $wpdb->prefix . "tw_configuration" );
        
        // Action
        $action = 'https://secure-stage.twispay.com';
        if ( $configuration ) {
            if ( $configuration->live_mode == 1 ) {
                $action = 'https://secure.twispay.com';
            }
        }
        
        // Site ID and Private Key
        $siteID = '';
        $privateKEY = '';
        if ( $configuration ) {
            if ( $configuration->live_mode == 1 ) {
                $siteID = $configuration->live_id;
                $privateKEY = $configuration->live_key;
            }
            else if ( $configuration->live_mode == 0 ) {
                $siteID = $configuration->staging_id;
                $privateKEY = $configuration->staging_key;
            }
        }
        
        // Address
        $inputs['address'] = '';
        $inputs['address'] = $data['billing']['address_1'];
        if ( ! $inputs['address'] ) {
            $inputs['address'] = $data['shipping']['address_1'];
        }
        
        // Amount
        $inputs['amount'] = '';
        $inputs['amount'] = $data['total'];
        
        // Back URL
        $inputs['backUrl'] = get_home_url() . '/twispay-confirmation?order_id=' . $_GET['order_id'] . '&secure_key=' . $data['cart_hash'];
        
        // CardID
        $inputs['cardId'] = '0';
        
        // CardTransactionMode
        $inputs['cardTransactionMode'] = 'authAndCapture';
        
        // City
        $inputs['city'] = '';
        $inputs['city'] = $data['billing']['city'];
        if ( ! $inputs['city'] ) {
            $inputs['city'] = $data['shipping']['city'];
        }
        
        // Country
        $inputs['country'] = '';
        $inputs['country'] = $data['billing']['country'];
        if ( ! $inputs['country'] ) {
            $inputs['country'] = $data['shipping']['country'];
        }
        
        // Currency
        $inputs['currency'] = '';
        $inputs['currency'] = $data['currency'];
        
        // CustomerTags
        $inputs['customerTags[0]'] = '';
        
        // Description
        $inputs['description'] = '';
        
        // Email
        $inputs['email'] = '';
        $inputs['email'] = $data['billing']['email'];
        
        // Firstname
        $inputs['firstName'] = '';
        $inputs['firstName'] = $data['billing']['first_name'];
        if ( ! $inputs['firstName'] ) {
            $inputs['firstName'] = $data['shipping']['first_name'];
        }
        
        // Identifier
        $inputs['identifier'] = '';
        $inputs['identifier'] = '_' . $data['customer_id'];
        
        // InvoiceEmail
        $inputs['invoiceEmail'] = '';
        
        // Item names
        $items = $order->get_items();
        $i = 0;
        foreach ( $items as $item ) {
            $inputs['item[' . $i . ']'] = $item['name'];
            $inputs['units[' . $i . ']'] = $item['quantity'];
            $inputs['subTotal[' . $i . ']'] = number_format( ( float )$item['subtotal'], 2 );
            $inputs['unitPrice[' . $i . ']'] = number_format( number_format( ( float )$item['subtotal'], 2) / number_format( ( float )$item['quantity'], 2 ), 2 );
            
            $i += 1;
        }
        
        // Lastname
        $inputs['lastName'] = '';
        $inputs['lastName'] = $data['billing']['last_name'];
        if ( ! $inputs['lastName'] ) {
            $inputs['lastName'] = $data['shipping']['last_name'];
        }
        
        // Order ID
        $inputs['orderId'] = '';
        if ( isset( $_GET['tw_reload'] ) && $_GET['tw_reload'] ) {
            $inputs['orderId'] = $data['id'] . '_' . time();
        }
        else {
            $inputs['orderId'] = $data['id'];
        }
        
        // Order tags
        $inputs['orderTags[0]'] = '';
        
        // Order type
        $inputs['orderType'] = 'purchase';
        
        // Site ID
        $inputs['siteId'] = $siteID;
        
        // Postcode
        $inputs['zipCode'] = $data['billing']['postcode'];
        if ( ! $inputs['zipCode'] ) {
            $inputs['zipCode'] = $data['shipping']['postcode'];
        }
        
        // Checksum
        ksort($inputs);
        $query = http_build_query($inputs);
        $encoded = hash_hmac('sha512', $query, $privateKEY, true);
        $checksum = '';
        $checksum = base64_encode($encoded);
        
        ?>
            <form accept-charset="UTF-8" id="twispay_payment_form" action="<?= $action; ?>" method="POST">
                <input type="hidden" name="address" value="<?= $inputs['address']; ?>">
                <input type="hidden" name="amount" value="<?= $inputs['amount']; ?>">
                <input type="hidden" name="backUrl" value="<?= $inputs['backUrl']; ?>">
                <input type="hidden" name="cardId" value="<?= $inputs['cardId']; ?>">
                <input type="hidden" name="cardTransactionMode" value="<?= $inputs['cardTransactionMode']; ?>">
                <input type="hidden" name="city" value="<?= $inputs['city']; ?>">
                <input type="hidden" name="country" value="<?= $inputs['country']; ?>">
                <input type="hidden" name="currency" value="<?= $inputs['currency']; ?>">
                <input type="hidden" name="customerTags[0]" value="<?= $inputs['customerTags[0]']; ?>">
                <input type="hidden" name="description" value="<?= $inputs['description']; ?>">
                <input type="hidden" name="email" value="<?= $inputs['email']; ?>">
                <input type="hidden" name="firstName" value="<?= $inputs['firstName']; ?>">
                <input type="hidden" name="identifier" value="<?= $inputs['identifier']; ?>">
                <input type="hidden" name="invoiceEmail" value="<?= $inputs['invoiceEmail']; ?>">
                <?php
                    $i = 0;
                    foreach ( $items as $key => $item ) {
                        echo '<input type="hidden" name="item[' . $i . ']" value="' . $item['name'] . '">';
                        
                        $i += 1;
                    }
                ?>
                <input type="hidden" name="lastName" value="<?= $inputs['lastName']; ?>">
                <input type="hidden" name="orderId" value="<?= $inputs['orderId']; ?>">
                <input type="hidden" name="orderTags[0]" value="<?= $inputs['orderTags[0]']; ?>">
                <input type="hidden" name="orderType" value="<?= $inputs['orderType']; ?>">
                <input type="hidden" name="siteId" value="<?= $inputs['siteId']; ?>">
                
                <?php
                    $i = 0;
                    foreach ( $items as $key => $item ) {
                        echo '<input type="hidden" name="subTotal[' . $i . ']" value="' . number_format( ( float )$item['subtotal'], 2 ) . '">';
                        echo '<input type="hidden" name="unitPrice[' . $i . ']" value="' . number_format( number_format( ( float )$item['subtotal'], 2) / number_format( ( float )$item['quantity'], 2 ), 2 ) . '">';
                        echo '<input type="hidden" name="units[' . $i . ']" value="' . $item['quantity'] . '">';
                        
                        $i += 1;
                    }
                ?>
                
                <input type="hidden" name="zipCode" value="<?= $inputs['zipCode']; ?>">
                <input type="hidden" name="checksum" value="<?= $checksum; ?>">
            </form>
            <script>document.getElementById( 'twispay_payment_form' ).submit();</script>
        <?php
    }
    else {
        echo '<style>.loader {display: none;}</style>';
        die( $tw_lang['twispay_processor_error'] );
    }
}
else {
    echo '<style>.loader {display: none;}</style>';
    die( $tw_lang['twispay_processor_error'] );
}
<?php
/**
 * Twispay Payment Validation
 *
 *
 * @package  Twispay/Front
 * @category Front
 * @author   @TODO
 * @version  0.0.1
 */

function twispay_tw_log( $string = false ) {
    $log_file = dirname( __FILE__ ) . '/../twispay-log.txt';
    
    if ( ! $string ) {
        $string = PHP_EOL . PHP_EOL;
    }
    else {
        $string = "[" . date( 'Y-m-d H:i:s' ) . "] " . $string;
    }
    
    @file_put_contents( $log_file, $string . PHP_EOL, FILE_APPEND );
}

function twispay_tw_twispayDecrypt( $encrypted, $apiKey ) {
    $encrypted = ( string )$encrypted;
    
    if ( ! strlen( $encrypted ) ) {
        return null;
    }
    
    if ( strpos( $encrypted, ',' ) !== false ) {
        $encryptedParts = explode( ',', $encrypted, 2 );
        $iv = base64_decode( $encryptedParts[0] );
        if ( false === $iv ) {
            return false;
        }
        
        $encrypted = base64_decode( $encryptedParts[1] );
        if ( false === $encrypted ) {
            return false;
        }
        
        $decrypted = openssl_decrypt( $encrypted, 'aes-256-cbc', $apiKey, OPENSSL_RAW_DATA, $iv );
        
        if ( false === $decrypted ) {
            return false;
        }
        
        return $decrypted;
    }
    
    return null;
}

function twispay_tw_getResultStatuses() {
    return array( 'complete-ok' );
}

function twispay_tw_logTransaction( $data ) {
    global $wpdb;
    global $woocommerce;
    
    try {
        $order = new WC_Order( $data['id_cart'] );
    }
    catch( Exception $e ) {
        
    }
    $checkout_url = $woocommerce->cart->get_checkout_url() . 'order-pay/' . $data['id_cart'] . '/?pay_for_order=true&key=' . $order->get_data()['order_key'] . '&tw_reload=true';
    
    $already = $wpdb->get_row( "SELECT * FROM " . $wpdb->prefix . "twispay_tw_transactions WHERE transactionId = '" . $data['transactionId'] . "'" );
    
    if ( $already ) {
        $wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "twispay_tw_transactions SET status = '" . $data['status'] . "' WHERE transactionId = '%d'", $data['transactionId'] ) );
    }
    else {
        $wpdb->get_results( "INSERT INTO `" . $wpdb->prefix . "twispay_tw_transactions` (`status`, `id_cart`, `identifier`, `orderId`, `transactionId`, `customerId`, `cardId`, `checkout_url`) VALUES ('" . $data['status'] . "', '" . $data['id_cart'] . "', '" . $data['identifier'] . "', '" . $data['orderId'] . "', '" . $data['transactionId'] . "', '" . $data['customerId'] . "', '" . $data['cardId'] . "', '" . $checkout_url . "');" );
    }
}

function twispay_tw_checkValidation( $decrypted, $usingOpenssl = true, $tw_order, $tw_lang ) {
    $json = json_decode( $decrypted );
    $tw_errors = array();
    
    if ( ! $json ) {
        return false;
    }
    
    if ( $usingOpenssl == false ) {
        twispay_tw_log( $tw_lang['log_s_decrypted'] . $decrypted );
    }
    
    if ( empty( $json->status ) && empty( $json->transactionStatus ) ) {
        $tw_errors[] = $tw_lang['log_empty_status'];
    }
     
    if ( empty( $json->identifier ) ) {
        $tw_errors[] = $tw_lang['log_empty_identifier'];
    }
    
    if ( empty( $json->externalOrderId ) ) {
        $tw_errors[] = $tw_lang['log_empty_external'];
    }
    
    if ( empty( $json->transactionId ) ) {
        $tw_errors[] = $tw_lang['log_empty_transaction'];
    }
    
    if ( sizeof( $tw_errors ) ) {
        foreach ( $tw_errors as $err ) {
            twispay_tw_log( $tw_lang['log_general_error'] . $err );
        }
        
        return false;
    } else {
        $data = array(
            'id_cart'          => explode( '_', $json->externalOrderId )[0],
            'status'           => ( empty( $json->status ) ) ? $json->transactionStatus : $json->status,
            'identifier'       => $json->identifier,
            'orderId'          => ( int )$json->orderId,
            'transactionId'    => ( int )$json->transactionId,
            'customerId'       => ( int )$json->customerId,
            'cardId'           => ( ! empty( $json->cardId ) ) ? ( int )$json->cardId : 0
        );
        twispay_tw_log( $tw_lang['log_general_response_data'] . json_encode( $data ) );
        
        if ( ! in_array( $data['status'], twispay_tw_getResultStatuses() ) ) {
            twispay_tw_log( sprintf( $tw_lang['log_wrong_status'], $data['status'] ) );
            
            twispay_tw_logTransaction( $data );
            
            return '0x1ds';
        }
        twispay_tw_log( $tw_lang['log_status_complete'] );
        
        twispay_tw_logTransaction( $data );
        twispay_tw_log( sprintf( $tw_lang['log_validating_complete'], $data['id_cart'] ) );
        
        return true;
    }
}

if ( isset( $_POST['opensslResult'] ) && $_POST['opensslResult'] ) {
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
    
    // Get configuration from database
    global $wpdb;
    $apiKey = '';
    $configuration = $wpdb->get_row( "SELECT * FROM " . $wpdb->prefix . "twispay_tw_configuration" );
    
    if ( $configuration ) {
        if ( $configuration->live_mode == 1 ) {
            $apiKey = $configuration->live_key;
        }
        else if ( $configuration->live_mode == 0 ) {
            $apiKey = $configuration->staging_key;
        }
    }
    
    if ( $apiKey ) {
        $decrypted = twispay_tw_twispayDecrypt( $_POST['opensslResult'], $apiKey );
        $json = json_decode( $decrypted );
        
        $order = '';
    
        try {
            $order = new WC_Order( explode( '_', $json->externalOrderId )[0] );
        }
        catch( Exception $e ) {
            
        }
        
        if ( $order ) {
            if ( $decrypted ) {
                $orderValidation = twispay_tw_checkValidation( $decrypted, true, $order->get_data(), $tw_lang );
                
                if ($orderValidation == '0x1ds') {
                    $json = json_decode( $decrypted );
                    $status = ( empty( $json->status ) ) ? $json->transactionStatus : $json->status;
                    
                    if ( $status == 'refund-ok' ) {
                        // Mark order as refunded
                        $order->update_status('refunded', __( $tw_lang['wa_order_refunded_notice'], 'woocommerce' ));
                    }
                    else if ( $status == 'cancel-ok' ) {
                        // Mark order as cancelled
                        $order->update_status('cancelled', __( $tw_lang['wa_order_cancelled_notice'], 'woocommerce' ));
                    }
                    
                    die( 'OK' );
                }
                else if( $orderValidation == true ) {
                    // Mark order as completed
                    $order->update_status('completed', __( $tw_lang['wa_order_status_notice'], 'woocommerce' ));
                    
                    die( 'OK' );
                }
                else {
                    die( 'ERROR' );
                }
            }
            else {
                twispay_tw_log( $tw_lang['log_decryption_error'] );
                twispay_tw_log( $tw_lang['log_openssl'] . $_GET['opensslResult'] );
                twispay_tw_log( $tw_lang['log_decrypted_string'] . $decrypted );
                die( 'ERROR' );
            }
        }
        else {
            die( 'NO ORDER RECORDED' );
        }
    }
    else {
        die( 'NO PRIVATE KEY' );
    }
}
die( 'NO DATA SENT' );
?>
<?php
/**
 * Twispay Payment Confirmation Form
 *
 * Html Payment Confirmation Form
 *
 * @package  Twispay/Front
 * @category Front
 * @author   @TODO
 * @version  0.0.1
 */
 
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
        $tw_errors[] = 'Empty transactionId';
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
            
            return false;
        }
        twispay_tw_log( $tw_lang['log_status_complete'] );
        
        twispay_tw_logTransaction( $data );
        twispay_tw_log( sprintf( $tw_lang['log_validating_complete'], $data['id_cart'] ) );
        
        return true;
    }
}

// Load languages
$lang = explode( '-', get_bloginfo( 'language' ) );
$lang = $lang[0];
if ( file_exists( TWISPAY_PLUGIN_DIR . 'lang/' . $lang . '/lang.php' ) ) {
    require( TWISPAY_PLUGIN_DIR . 'lang/' . $lang . '/lang.php' );
} else {
    require( TWISPAY_PLUGIN_DIR . 'lang/en/lang.php' );
}

if ( ! class_exists( 'WooCommerce' ) ) {
    ?>
        <div class="error notice" style="margin-top: 20px;">
            <h3><?= $tw_lang['general_error_title']; ?></h3>
            <p><?= $tw_lang['no_woocommerce_f']; ?> <a target="_blank" href="https://wordpress.org/plugins/woocommerce/"><?= $tw_lang['no_woocommerce_s']; ?></a>.</p>
        </div>
    <?php
}
else {
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
    
    if ( isset( $_POST['result'] ) && $_POST['result'] ) {
        if ( isset( $_GET['order_id'] ) && $_GET['order_id'] ) {
            if ( isset( $_GET['secure_key'] ) && $_GET['secure_key'] ) {
                if ( isset( $_POST['opensslResult'] ) && $_POST['opensslResult'] ) {
                    $result = $_POST['opensslResult'];
                }
                else {
                    $result = $_POST['result'];
                }
                
                if ( $apiKey ) {
                    $order = '';
    
                    try {
                        $order = new WC_Order( $_GET['order_id'] );
                    }
                    catch( Exception $e ) {
                        
                    }
                    
                    if ( $order ) {
                        if ( $order->get_data()['cart_hash'] == $_GET['secure_key'] ) {
                            global $woocommerce;
                            
                            $decrypted = twispay_tw_twispayDecrypt( $result, $apiKey );
                            $orderValidation = twispay_tw_checkValidation( $decrypted, false, $order->get_data(), $tw_lang );
                            $json = json_decode( $decrypted );
                            $status = ( empty( $json->status ) ) ? $json->transactionStatus : $json->status;
                            $checkout_url = $woocommerce->cart->get_checkout_url() . 'order-pay/' . $_GET['order_id'] . '/?pay_for_order=true&key=' . $order->get_data()['order_key'] . '&tw_reload=true';
                            
                            if ( $orderValidation && $status == 'complete-ok' ) {
                                // Mark order as completed
                                $order->update_status('completed', __( $tw_lang['wa_order_status_notice'], 'woocommerce' ));
                                
                                // Redirect to Thank you Page if it is set, if not, redirect to default Thank you Page
                                if ( $configuration->thankyou_page ) {
                                    wp_safe_redirect( $configuration->thankyou_page );
                                    
                                    twispay_tw_log();
                                }
                                else {
                                    class WC_Gateway_Twispay_Thankyou extends WC_Payment_Gateway {
                                        /**
                                         * Twispay Gateway Constructor
                                         *
                                         * @public
                                         * @return void
                                         */
                                        public function __construct( $order ) {
                                            wp_safe_redirect( $this->get_return_url( $order ) );
                                            
                                            twispay_tw_log();
                                        }
                                    }
                                    
                                    new WC_Gateway_Twispay_Thankyou( $order );
                                }
                            }
                            else {
                                ?>
                                    <div class="error notice" style="margin-top: 20px;">
                                        <h3><?= $tw_lang['general_error_title']; ?></h3>
                                        <p><?= ( $configuration->contact_email == '0' ? ( str_replace( '[try again]', '<a href="' . $checkout_url . '">' . $tw_lang['general_error_desc_try_again'] . '</a>', $tw_lang['general_error_desc_f'] ) ) . $tw_lang['general_error_desc_s'] : ( str_replace( '[try again]', '<a href="' . $checkout_url . '">' . $tw_lang['general_error_desc_try_again'] . '</a>', $tw_lang['general_error_desc_f'] ) ) . '<a href="mailto:' . $configuration->contact_email . '">' . $tw_lang['general_error_desc_s'] . '</a>' ); ?></p>
                                    </div>
                                <?php
                            }
                        }
                        else {
                            ?>
                                <div class="error notice" style="margin-top: 20px;">
                                    <h3><?= $tw_lang['general_error_title']; ?></h3>
                                    <p><?= $tw_lang['general_error_invalid_key']; ?></p>
                                </div>
                            <?php
                        }
                    }
                    else {
                        ?>
                            <div class="error notice" style="margin-top: 20px;">
                                <h3><?= $tw_lang['general_error_title']; ?></h3>
                                <p><?= $tw_lang['general_error_invalid_order']; ?></p>
                            </div>
                        <?php
                    }
                }
                else {
                    ?>
                        <div class="error notice" style="margin-top: 20px;">
                            <h3><?= $tw_lang['general_error_title']; ?></h3>
                            <p><?= $tw_lang['general_error_invalid_private']; ?></p>
                        </div>
                    <?php
                }
            }
            else {
                ?>
                    <div class="error notice" style="margin-top: 20px;">
                        <h3><?= $tw_lang['general_error_title']; ?></h3>
                        <p><?= ( $configuration->contact_email == '0' ? ( str_replace( '[try again]', '<a href="' . $checkout_url . '">' . $tw_lang['general_error_desc_try_again'] . '</a>', $tw_lang['general_error_desc_f'] ) ) . $tw_lang['general_error_desc_s'] : ( str_replace( '[try again]', '<a href="' . $checkout_url . '">' . $tw_lang['general_error_desc_try_again'] . '</a>', $tw_lang['general_error_desc_f'] ) ) . '<a href="mailto:' . $configuration->contact_email . '">' . $tw_lang['general_error_desc_s'] . '</a>' ); ?></p>
                    </div>
                <?php
            }
        }
        else {
            ?>
                <div class="error notice" style="margin-top: 20px;">
                    <h3><?= $tw_lang['general_error_title']; ?></h3>
                    <p><?= ( $configuration->contact_email == '0' ? ( str_replace( '[try again]', '<a href="' . $checkout_url . '">' . $tw_lang['general_error_desc_try_again'] . '</a>', $tw_lang['general_error_desc_f'] ) ) . $tw_lang['general_error_desc_s'] : ( str_replace( '[try again]', '<a href="' . $checkout_url . '">' . $tw_lang['general_error_desc_try_again'] . '</a>', $tw_lang['general_error_desc_f'] ) ) . '<a href="mailto:' . $configuration->contact_email . '">' . $tw_lang['general_error_desc_s'] . '</a>' ); ?></p>
                </div>
            <?php
        }
    }
    else {
        ?>
            <div class="error notice" style="margin-top: 20px;">
                <h3><?= $tw_lang['general_error_title']; ?></h3>
                <p><?= ( $configuration->contact_email == '0' ? ( str_replace( '[try again]', '<a href="' . $checkout_url . '">' . $tw_lang['general_error_desc_try_again'] . '</a>', $tw_lang['general_error_desc_f'] ) ) . $tw_lang['general_error_desc_s'] : ( str_replace( '[try again]', '<a href="' . $checkout_url . '">' . $tw_lang['general_error_desc_try_again'] . '</a>', $tw_lang['general_error_desc_f'] ) ) . '<a href="mailto:' . $configuration->contact_email . '">' . $tw_lang['general_error_desc_s'] . '</a>' ); ?></p>
            </div>
        <?php
    }
}


<?php
/**
 * Twispay Transaction Log Admin Page
 *
 * Twispay transaction log page on the Administrator dashboard
 *
 * @package  Twispay/Admin
 * @category Admin
 * @author   Twispay
 */

// Exit if the file is accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function twispay_tw_transaction_log_administrator() {
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
                <p><?= esc_html( $tw_lang['no_woocommerce_f'] ); ?> <a target="_blank" href="https://wordpress.org/plugins/woocommerce/"><?= esc_html( $tw_lang['no_woocommerce_s'] ); ?></a>.</p>
                <div class="clearfix"></div>
            </div>
        <?php
    }
    else {
        ?>
            <div class="wrap">
                <h1><?= esc_html( $tw_lang['transaction_log_title'] ); ?></h1>
                <p><?= esc_html( $tw_lang['transaction_log_subtitle'] ); ?></p>
                <?php
                    if ( file_exists( TWISPAY_PLUGIN_DIR . 'twispay-log.txt' ) ) {
                        echo '<textarea readonly style="width: 900px; height: 386px; margin-top: 10px;">' . wp_kses( file_get_contents( TWISPAY_PLUGIN_DIR . 'twispay-log.txt' ), wp_kses_allowed_html( 'strip' ) ) . '</textarea>';
                    } else {
                        echo '<p>' . esc_html( $tw_lang['transaction_log_no_log'] ) . '</p>';
                    }
                ?>
            </div>
        <?php
    }
}

<?php
/**
 * Twispay Transaction Log Admin Page
 *
 * Twispay transaction log page on the Administrator dashboard
 *
 * @package  Twispay/Admin
 * @category Admin
 * @author   Twispay
 * @version  1.0.8
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
                <p><?= $tw_lang['no_woocommerce_f']; ?> <a target="_blank" href="https://wordpress.org/plugins/woocommerce/"><?= $tw_lang['no_woocommerce_s']; ?></a>.</p>
                <div class="clearfix"></div>
            </div>
        <?php
    }
    else {
        ?>
            <div class="wrap">
                <h1><?= $tw_lang['transaction_log_title']; ?></h1>
                <p><?= $tw_lang['transaction_log_subtitle']; ?></p>
                <?php
                    if ( file_exists( TWISPAY_PLUGIN_DIR . 'twispay-log.txt' ) ) {
                        echo '<textarea readonly style="width: 900px; height: 386px; margin-top: 10px;">' . file_get_contents( TWISPAY_PLUGIN_DIR . 'twispay-log.txt' ) . '</textarea>';
                    } else {
                        echo '<p>' . $tw_lang['transaction_log_no_log'] . '</p>';
                    }
                ?>
            </div>
        <?php
    }
}

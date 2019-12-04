<?php
/**
 * Twispay Refund Transaction
 *
 * Refund transaction html form
 *
 * @package  Twispay/Admin
 * @category Admin
 * @author   Twispay
 * @version  1.0.8
 */

// Load languages
$lang = explode( '-', get_bloginfo( 'language' ) );
$lang = $lang[0];
if ( file_exists( TWISPAY_PLUGIN_DIR . 'lang/' . $lang . '/lang.php' ) ) {
    require( TWISPAY_PLUGIN_DIR . 'lang/' . $lang . '/lang.php' );
} else {
    require( TWISPAY_PLUGIN_DIR . 'lang/en/lang.php' );
}

?>
<div class="wrap">
    <h2><?= $tw_lang['transaction_list_refund_ptitle']; ?></h2>
    <p><?= $tw_lang['transaction_list_refund_subtitle']; ?></p>

    <!-- Get all payment transactions ID from the $_GET parameters -->
    <?php
        if ( isset( $_GET['payment_ad'] ) && $_GET['payment_ad'] ) {
            foreach ( explode( ',', $_GET['payment_ad'] ) as $key => $a_id ) {
                print_r( 'ID: #' . $a_id );
                print_r( '<br>' );
            }
        }
    ?>

    <form method="post" id="refund_payment_transaction">
        <input type="hidden" name="tw_general_action" value="refund_payment_transaction" />
        <?php submit_button( $tw_lang['transaction_list_confirm_title'], 'primary', 'createuser', true, array( 'id' => 'confirmdeletion' ) ); ?>
    </form>
</div>

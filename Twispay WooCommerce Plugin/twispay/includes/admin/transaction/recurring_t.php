<?php
/**
 * Twispay Recurring Transaction
 *
 * Recurring transaction html form
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
    <h2><?= $tw_lang['transaction_list_recurring_ptitle']; ?></h2>
    <p><?= $tw_lang['transaction_list_recurring_subtitle']; ?></p>

    <!-- Get all payment order ID from the $_GET parameters -->
    <?php
        if ( isset( $_GET['order_ad'] ) && $_GET['order_ad'] ) {
            foreach ( explode( ',', $_GET['order_ad'] ) as $key => $a_id ) {
                print_r( 'ID: #' . $a_id );
                print_r( '<br>' );
            }
        }
    ?>

    <form method="post" id="recurring_order">
        <input type="hidden" name="tw_general_action" value="recurring_order" />
        <?php submit_button( $tw_lang['transaction_list_confirm_title'], 'primary', 'createuser', true, array( 'id' => 'confirmdeletion' ) ); ?>
    </form>
</div>

<?php
/**
 * Twispay Transaction List Admin Page
 *
 * Twispay transaction list page on the Administrator dashboard
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

// Import the table class
require_once TWISPAY_PLUGIN_DIR . 'includes/admin/transaction/transaction-table.php';

function twispay_tw_transaction_administrator() {
    /* Load languages */
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
        // Check if the view / edit / delete action is detected, otherwise load the campaigns form
        if ( isset( $_GET['action'] ) && $_GET['action'] ) {
            $action = $_GET['action'];

            switch ( $action ) {
                case 'refund_payment':
                    include TWISPAY_PLUGIN_DIR . 'includes/admin/transaction/refund_t.php';
                    break;
                case 'recurring_payment':
                    include TWISPAY_PLUGIN_DIR . 'includes/admin/transaction/recurring_t.php';
                    break;
            }
        }
        else {
            ?>
                <div class="wrap">
                    <h1><?= $tw_lang['transaction_title']; ?></h1>

                    <?php if( class_exists('WC_Subscriptions') ){ ?>
                        <form method="post" id="synchronize_subscriptions">
                            <table class="form-table">
                                <tr class="form-field" id="contact_email_o">
                                    <th scope="row"><label><?= $tw_lang['subscriptions_sync_label']; ?></span></label></th>
                                    <td>
                                        <input type="hidden" name="tw_general_action" value="synchronize_subscriptions" />
                                        <?php submit_button( $tw_lang['subscriptions_sync_button'], 'primary', 'createuser', true, array( 'id' => 'synchronizesubscriptions' ) ); ?>
                                        <p class="description"><?= $tw_lang['subscriptions_sync_desc']; ?></p>
                                    </td>
                                </tr>
                            </table>
                        </form>
                    <?php } ?>


                    <?php
                        if ( isset( $_GET['notice'] ) && $_GET['notice'] ) {
                            $notice = $_GET['notice'];

                            switch ( $notice ) {
                                case 'error_refund':
                                    ?>
                                        <div class="error notice">
                                            <p><?= $tw_lang['transaction_error_refund']; ?></p>
                                        </div>
                                    <?php
                                    break;
                                case 'error_recurring':
                                    ?>
                                        <div class="error notice">
                                            <p><?= $tw_lang['transaction_error_recurring']; ?></p>
                                        </div>
                                    <?php
                                    break;
                                case 'success_refund':
                                    ?>
                                        <div class="updated notice">
                                            <p><?= $tw_lang['transaction_success_refund']; ?></p>
                                        </div>
                                    <?php
                                    break;
                                case 'success_recurring':
                                    ?>
                                        <div class="updated notice">
                                            <p><?= $tw_lang['transaction_success_recurring']; ?></p>
                                        </div>
                                    <?php
                                    break;
                                case 'sync_finished':
                                    ?>
                                        <div class="updated notice">
                                            <p><?= $tw_lang['transaction_sync_finished']; ?></p>
                                        </div>
                                    <?php
                                    break;
                                case 'errorp_refund':
                                    ?>
                                        <div class="error notice">
                                            <p><?= $_GET['emessage']; ?></p>
                                        </div>
                                    <?php
                                    break;
                            }
                        }

                        // Create the Payment Methods object and build the Table
                        $transaction_table = new Twispay_TransactionTable( $tw_lang );
                        $transaction_table->views();
                    ?>

                    <form method="get">
                        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
                        <?php $transaction_table->search_box( $tw_lang['transaction_list_search_title'], 'search-query' ); ?>
                    </form>
                    <form method="post">
                        <?php $transaction_table->prepare_items(); $transaction_table->display(); ?>
                    </form>
                </div>
            <?php
        }
    }
}

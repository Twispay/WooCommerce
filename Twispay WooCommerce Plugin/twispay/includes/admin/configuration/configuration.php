<?php
/**
 * Twispay Configuration Admin Page
 *
 * Twispay general configuration page on the Administrator dashboard
 *
 * @package  Twispay/Admin
 * @category Admin
 * @author   Twispay
 */

// Exit if the file is accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function twispay_tw_configuration() {
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
    } else {
        ?>
            <div class="wrap">
                <h2><?= esc_html( $tw_lang['configuration_title'] ); ?></h2>
                <?php
                    if ( isset( $_GET['notice'] ) && sanitize_text_field( $_GET['notice'] ) ) {
                        $notice = sanitize_text_field( $_GET['notice'] );

                        switch ( $notice ) {
                            case 'edit_configuration':
                                ?>
                                    <div class="updated notice">
                                        <p><?= esc_html( $tw_lang['configuration_edit_notice'] ); ?></p>
                                    </div>
                                <?php
                            break;
                        }
                    }
                ?>

                <p><?= esc_html( $tw_lang['configuration_subtitle'] ); ?></p>
                <form method="post" id="general_configuration">
                    <table class="form-table">
                        <tr class="form-field form-required">
                            <th scope="row"><label for="live_mode"><?= esc_html( $tw_lang['live_mode_label'] ); ?></label></th>
                            <td>
                                <?= twispay_tw_get_live_mode( $tw_lang ); ?>
                                <p class="description"><?= esc_html( $tw_lang['live_mode_desc'] ); ?></p>
                            </td>
                        </tr>
                        <tr class="form-field" id="staging_site_id">
                            <th scope="row"><label for="staging_site_id"><?= esc_html( $tw_lang['staging_id_label'] ); ?></span></label></th>
                            <td>
                                <input name="staging_site_id" type="text" value="<?= esc_attr( twispay_tw_get_staging_site_id() ); ?>" style="max-width: 400px;" />
                                <p class="description"><?= esc_html( $tw_lang['staging_id_desc'] ); ?> <a target="_blank" href="https://merchant-stage.twispay.com/"><?= esc_html( $tw_lang['r_custom_thankyou_desc_s'] ); ?></a>.</p>
                            </td>
                        </tr>
                        <tr class="form-field" id="staging_private_key">
                            <th scope="row"><label for="staging_private_key"><?= esc_html( $tw_lang['staging_key_label'] ); ?></span></label></th>
                            <td>
                                <input name="staging_private_key" type="text" value="<?= esc_attr( twispay_tw_get_staging_private_key() ); ?>" style="max-width: 400px;" />
                                <p class="description"><?= esc_html( $tw_lang['staging_key_desc'] ); ?> <a target="_blank" href="https://merchant-stage.twispay.com/"><?= esc_html( $tw_lang['r_custom_thankyou_desc_s'] ); ?></a>.</p>
                            </td>
                        </tr>
                        <tr class="form-field" id="live_site_id">
                            <th scope="row"><label for="live_site_id"><?= esc_html( $tw_lang['live_id_label'] ); ?></span></label></th>
                            <td>
                                <input name="live_site_id" type="text" value="<?= esc_attr( twispay_tw_get_live_site_id() ); ?>" style="max-width: 400px;" />
                                <p class="description"><?= esc_html( $tw_lang['live_id_desc'] ); ?> <a target="_blank" href="https://merchant.twispay.com/"><?= esc_html( $tw_lang['r_custom_thankyou_desc_s'] ); ?></a>.</p>
                            </td>
                        </tr>
                        <tr class="form-field" id="live_private_key">
                            <th scope="row"><label for="live_private_key"><?= esc_html( $tw_lang['live_key_label'] ); ?></span></label></th>
                            <td>
                                <input name="live_private_key" type="text" value="<?= esc_attr( twispay_tw_get_live_private_key() ); ?>" style="max-width: 400px;" />
                                <p class="description"><?= esc_html( $tw_lang['live_key_desc'] ); ?> <a target="_blank" href="https://merchant.twispay.com/"><?= esc_html( $tw_lang['r_custom_thankyou_desc_s'] ); ?></a>.</p>
                            </td>
                        </tr>
                        <tr class="form-field" id="s_t_s_notification">
                            <th scope="row"><label for="s_t_s_notification"><?= esc_html( $tw_lang['s_t_s_notification_label'] ); ?></span></label></th>
                            <td>
                                <input name="s_t_s_notification" disabled="disabled" type="text" value="<?= home_url($path = '?twispay-ipn'); ?>" style="max-width: 400px;" />
                                <p class="description"><?= esc_html( $tw_lang['s_t_s_notification_desc'] ); ?></p>
                            </td>
                        </tr>
                        <tr class="form-field" id="r_custom_thankyou">
                            <th scope="row"><label for="r_custom_thankyou"><?= esc_html( $tw_lang['r_custom_thankyou_label'] ); ?></span></label></th>
                            <td>
                                <?= twispay_tw_get_wp_pages( $tw_lang ); ?>
                                <p class="description"><?= esc_html( $tw_lang['r_custom_thankyou_desc_f'] ); ?> <a href="<?= get_admin_url() . 'post-new.php?post_type=page'; ?>"><?= esc_html( $tw_lang['r_custom_thankyou_desc_s'] ); ?></a>.</p>
                            </td>
                        </tr>
                        <tr class="form-field" id="suppress_email">
                            <th scope="row"><label for="suppress_email"><?= esc_html( $tw_lang['suppress_email_label'] ); ?></span></label></th>
                            <td>
                                <?= twispay_tw_get_suppress_email( $tw_lang ); ?>
                                <p class="description"><?= esc_html( $tw_lang['suppress_email_desc'] ); ?></p>
                            </td>
                        </tr>
                        <tr class="form-field" id="contact_email_o">
                            <th scope="row"><label for="contact_email_o"><?= esc_html( $tw_lang['contact_email_o'] ); ?></span></label></th>
                            <td>
                                <input name="contact_email_o" type="text" value="<?= sanitize_email( twispay_tw_get_contact_email_o() == '0' ? '' : twispay_tw_get_contact_email_o() ); ?>" style="max-width: 400px;" />
                                <p class="description"><?= esc_html( $tw_lang['contact_email_o_desc'] ); ?></p>
                            </td>
                        </tr>
                        <tr class="form-field" id="contact_email_o">
                            <th scope="row">
                                <input type="hidden" name="tw_general_action" value="edit_general_configuration" />
                                <?php submit_button( esc_attr( $tw_lang['configuration_save_button'] ), 'primary', 'edituser', true, array( 'id' => 'ceditusersub' ) ); ?>
                            </th>
                            <td></td>
                        </tr>
                    </table>
                </form>
            </div>
        <?php
    }
}

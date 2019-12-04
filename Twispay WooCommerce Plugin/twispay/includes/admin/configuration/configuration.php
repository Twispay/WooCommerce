<?php
/**
 * Twispay Configuration Admin Page
 *
 * Twispay general configuration page on the Administrator dashboard
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
                <p><?= $tw_lang['no_woocommerce_f']; ?> <a target="_blank" href="https://wordpress.org/plugins/woocommerce/"><?= $tw_lang['no_woocommerce_s']; ?></a>.</p>
                <div class="clearfix"></div>
            </div>
        <?php
    } else {
        ?>
            <div class="wrap">
                <h2><?= $tw_lang['configuration_title']; ?></h2>
                <?php
                    if ( isset( $_GET['notice'] ) && $_GET['notice'] ) {
                        $notice = $_GET['notice'];

                        switch ( $notice ) {
                            case 'edit_configuration':
                                ?>
                                    <div class="updated notice">
                                        <p><?= $tw_lang['configuration_edit_notice']; ?></p>
                                    </div>
                                <?php
                            break;
                        }
                    }
                ?>

                <p><?= $tw_lang['configuration_subtitle']; ?></p>
                <form method="post" id="general_configuration">
                    <table class="form-table">
                        <tr class="form-field form-required">
                            <th scope="row"><label for="live_mode"><?= $tw_lang['live_mode_label']; ?></label></th>
                            <td>
                                <?= twispay_tw_get_live_mode( $tw_lang ); ?>
                                <p class="description"><?= $tw_lang['live_mode_desc']; ?></p>
                            </td>
                        </tr>
                        <tr class="form-field" id="staging_site_id">
                            <th scope="row"><label for="staging_site_id"><?= $tw_lang['staging_id_label']; ?></span></label></th>
                            <td>
                                <input name="staging_site_id" type="text" value="<?= twispay_tw_get_staging_site_id(); ?>" style="max-width: 400px;" />
                                <p class="description"><?= $tw_lang['staging_id_desc']; ?> <a target="_blank" href="https://merchant-stage.twispay.com/login"><?= $tw_lang['r_custom_thankyou_desc_s']; ?></a>.</p>
                            </td>
                        </tr>
                        <tr class="form-field" id="staging_private_key">
                            <th scope="row"><label for="staging_private_key"><?= $tw_lang['staging_key_label']; ?></span></label></th>
                            <td>
                                <input name="staging_private_key" type="text" value="<?= twispay_tw_get_staging_private_key(); ?>" style="max-width: 400px;" />
                                <p class="description"><?= $tw_lang['staging_key_desc']; ?> <a target="_blank" href="https://merchant-stage.twispay.com/login"><?= $tw_lang['r_custom_thankyou_desc_s']; ?></a>.</p>
                            </td>
                        </tr>
                        <tr class="form-field" id="live_site_id">
                            <th scope="row"><label for="live_site_id"><?= $tw_lang['live_id_label']; ?></span></label></th>
                            <td>
                                <input name="live_site_id" type="text" value="<?= twispay_tw_get_live_site_id(); ?>" style="max-width: 400px;" />
                                <p class="description"><?= $tw_lang['live_id_desc']; ?> <a target="_blank" href="https://merchant.twispay.com/login"><?= $tw_lang['r_custom_thankyou_desc_s']; ?></a>.</p>
                            </td>
                        </tr>
                        <tr class="form-field" id="live_private_key">
                            <th scope="row"><label for="live_private_key"><?= $tw_lang['live_key_label']; ?></span></label></th>
                            <td>
                                <input name="live_private_key" type="text" value="<?= twispay_tw_get_live_private_key(); ?>" style="max-width: 400px;" />
                                <p class="description"><?= $tw_lang['live_key_desc']; ?> <a target="_blank" href="https://merchant.twispay.com/login"><?= $tw_lang['r_custom_thankyou_desc_s']; ?></a>.</p>
                            </td>
                        </tr>
                        <tr class="form-field" id="s_t_s_notification">
                            <th scope="row"><label for="s_t_s_notification"><?= $tw_lang['s_t_s_notification_label']; ?></span></label></th>
                            <td>
                                <input name="s_t_s_notification" disabled="disabled" type="text" value="<?= plugins_url() . '/twispay/includes/validation.php'; ?>" style="max-width: 400px;" />
                                <p class="description"><?= $tw_lang['s_t_s_notification_desc']; ?></p>
                            </td>
                        </tr>
                        <tr class="form-field" id="r_custom_thankyou">
                            <th scope="row"><label for="r_custom_thankyou"><?= $tw_lang['r_custom_thankyou_label']; ?></span></label></th>
                            <td>
                                <?= twispay_tw_get_wp_pages( $tw_lang ); ?>
                                <p class="description"><?= $tw_lang['r_custom_thankyou_desc_f']; ?> <a href="<?= get_admin_url() . 'post-new.php?post_type=page'; ?>"><?= $tw_lang['r_custom_thankyou_desc_s']; ?></a>.</p>
                            </td>
                        </tr>
                        <tr class="form-field" id="suppress_email">
                            <th scope="row"><label for="suppress_email"><?= $tw_lang['suppress_email_label']; ?></span></label></th>
                            <td>
                                <?= twispay_tw_get_suppress_email( $tw_lang ); ?>
                                <p class="description"><?= $tw_lang['suppress_email_desc']; ?></p>
                            </td>
                        </tr>
                        <tr class="form-field" id="contact_email_o">
                            <th scope="row"><label for="contact_email_o"><?= $tw_lang['contact_email_o']; ?></span></label></th>
                            <td>
                                <input name="contact_email_o" type="text" value="<?= ( twispay_tw_get_contact_email_o() == '0' ? '' : twispay_tw_get_contact_email_o() ); ?>" style="max-width: 400px;" />
                                <p class="description"><?= $tw_lang['contact_email_o_desc']; ?></p>
                            </td>
                        </tr>
                        <tr class="form-field" id="contact_email_o">
                            <th scope="row">
                                <input type="hidden" name="tw_general_action" value="edit_general_configuration" />
                                <?php submit_button( $tw_lang['configuration_save_button'], 'primary', 'edituser', true, array( 'id' => 'ceditusersub' ) ); ?>
                            </th>
                            <td></td>
                        </tr>
                    </table>
                </form>
            </div>
        <?php
    }
}

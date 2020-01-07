<?php
/**
 * Twispay Custom Functions
 *
 * Here stand all Twispay Functions
 *
 * @package  Twispay/Admin
 * @category Admin
 * @author   Twispay
 * @version  1.0.8
 */

/**
 * Retrieves Live Mode options from Configuration Panel
 *
 * @public
 * @return string Html with all Live Mode options
 */
function twispay_tw_get_live_mode( $tw_lang ) {
    // Wordpress database refference
    global $wpdb;
    $html = '';

    $live_mode = $wpdb->get_results( "SELECT live_mode FROM " . $wpdb->prefix . "twispay_tw_configuration" );

    if ( $live_mode ) {
        $html .= '<select name="live_mode" id="live_mode">';
        foreach ( $live_mode as $e_l ) {
            if ( $e_l->live_mode == 1 ) {
                $html .= '<option value="1" selected>' . $tw_lang['live_mode_option_true'] . '</option>';
                $html .= '<option value="0">' . $tw_lang['live_mode_option_false'] . '</option>';
            }
            else {
                $html .= '<option value="1">' . $tw_lang['live_mode_option_true'] . '</option>';
                $html .= '<option value="0"selected>' . $tw_lang['live_mode_option_false'] . '</option>';
            }

            break;
        }
        $html .= '</select>';

        return $html;
    }
    else {
        // If by any chance the configuration row does not exist, add default one immediately. ( tw_configuration table )
        $wpdb->insert( $wpdb->prefix . 'twispay_tw_configuration', array(
            'live_mode'     => 0
        ) );

        // Now display the default form
        $html .= '<select name="live_mode" id="live_mode">';
        $html .= '<option value="1">' . $tw_lang['live_mode_option_true'] . '</option>';
        $html .= '<option value="0" selected>' . $tw_lang['live_mode_option_false'] . '</option>';
        $html .= '</select>';

        return $html;
    }
}

/**
 * Retrieves Suppress Email options from Configuration Panel
 *
 * @public
 * @return string Html with all Suppress Email options
 */
function twispay_tw_get_suppress_email( $tw_lang ) {
    // Wordpress database refference
    global $wpdb;
    $html = '';

    $suppress_email = $wpdb->get_results( "SELECT suppress_email FROM " . $wpdb->prefix . "twispay_tw_configuration" );

    if ( $suppress_email ) {
        $html .= '<select name="suppress_email" id="suppress_email">';
        foreach ( $suppress_email as $e_s ) {
            if ( $e_s->suppress_email == 1 ) {
                $html .= '<option value="1" selected>' . $tw_lang['live_mode_option_true'] . '</option>';
                $html .= '<option value="0">' . $tw_lang['live_mode_option_false'] . '</option>';
            }
            else {
                $html .= '<option value="1">' . $tw_lang['live_mode_option_true'] . '</option>';
                $html .= '<option value="0"selected>' . $tw_lang['live_mode_option_false'] . '</option>';
            }

            break;
        }
        $html .= '</select>';

        return $html;
    }
}

/**
 * Retrieves all Wordpress Pages for configuring Thank you redirect
 *
 * @public
 * @return string Html with all Wordpress Pages options
 */
function twispay_tw_get_wp_pages( $tw_lang ) {
    // Wordpress database refference
    global $wpdb;
    $html = '';

    $configuration = $wpdb->get_results( "SELECT thankyou_page FROM " . $wpdb->prefix . "twispay_tw_configuration" );
    $wp_pages = $wpdb->get_results( "SELECT post_title, guid FROM " . $wpdb->prefix . "posts WHERE post_type = 'page' AND post_status = 'publish' " );

    if ( $wp_pages ) {
        $html .= '<select name="wp_pages" id="wp_pages">';
        $html .= '<option value="0">' . $tw_lang['get_all_wordpress_pages_default'] . '</option>';

        foreach ( $wp_pages as $e_p ) {
            if ( $e_p->post_title != 'Twispay confirmation' ) {
                if ( $configuration ) {
                    foreach ( $configuration as $e_c ) {
                        if ( $e_c->thankyou_page == $e_p->guid ) {
                            $html .= '<option value="' . $e_p->guid . '" selected>' . $e_p->post_title . '</option>';
                        }
                        else {
                            $html .= '<option value="' . $e_p->guid . '">' . $e_p->post_title . '</option>';
                        }
                        break;
                    }
                }
            }
        }
        $html .= '</select>';

        return $html;
    }
}

/**
 * Retrieves Contact email on the current Shop
 *
 * @public
 * @return string contact_email
 */
function twispay_tw_get_contact_email_o() {
    // Wordpress database refference
    global $wpdb;

    $contact_email = $wpdb->get_results( "SELECT contact_email FROM " . $wpdb->prefix . "twispay_tw_configuration" );

    if ( $contact_email ) {
        return $contact_email[0]->contact_email;
    }
    else {
        return '';
    }
}

/**
 * Retrieves Staging Site ID on the current Shop
 *
 * @public
 * @return string staging_id
 */
function twispay_tw_get_staging_site_id() {
    // Wordpress database refference
    global $wpdb;

    $staging_id = $wpdb->get_results( "SELECT staging_id FROM " . $wpdb->prefix . "twispay_tw_configuration" );

    if ( $staging_id ) {
        return $staging_id[0]->staging_id;
    }
    else {
        return '';
    }
}

/**
 * Retrieves Staging Private Key on the current Shop
 *
 * @public
 * @return string staging_key
 */
function twispay_tw_get_staging_private_key() {
    // Wordpress database refference
    global $wpdb;

    $staging_key = $wpdb->get_results( "SELECT staging_key FROM " . $wpdb->prefix . "twispay_tw_configuration" );

    if ( $staging_key ) {
        return $staging_key[0]->staging_key;
    }
    else {
        return '';
    }
}

/**
 * Retrieves Live Site ID on the current Shop
 *
 * @public
 * @return string live_id
 */
function twispay_tw_get_live_site_id() {
    // Wordpress database refference
    global $wpdb;

    $live_id = $wpdb->get_results( "SELECT live_id FROM " . $wpdb->prefix . "twispay_tw_configuration" );

    if ( $live_id ) {
        return $live_id[0]->live_id;
    }
    else {
        return '';
    }
}

/**
 * Retrieves Live Private Key on the current Shop
 *
 * @public
 * @return string live_key
 */
function twispay_tw_get_live_private_key() {
    // Wordpress database refference
    global $wpdb;

    $live_key = $wpdb->get_results( "SELECT live_key FROM " . $wpdb->prefix . "twispay_tw_configuration" );

    if ( $live_key ) {
        return $live_key[0]->live_key;
    }
    else {
        return '';
    }
}

?>

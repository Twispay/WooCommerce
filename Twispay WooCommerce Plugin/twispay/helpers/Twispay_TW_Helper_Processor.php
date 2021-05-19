<?php

class Twispay_TW_Helper_Processor {
    const LIVE_URL = 'https://secure.twispay.com';
    const STAGE_URL = 'https://secure-stage.twispay.com';

    public static function get_current_language() {
        return explode('-', get_bloginfo('language'))[0];
    }

    public static function format_phone($phone) {
        $output = '';

        if (empty($phone)) {
            return $output;
        }

        $output = $phone[0] ? '+' : '';

        return $output . preg_replace('/([^0-9]*)+/', '', $phone);
    }

    public static function get_configuration() {
        $configuration = self::query_configuration();
        $result = [];

        if ($configuration->live_mode === null) {
            return $result;
        }

        $is_live = $configuration->live_mode === '1';

        if ($is_live) {
            $result['is_live'] = true;
            $result['site_id'] = $configuration->live_id;
            $result['secret_key'] = $configuration->live_key;

            return $result;
        }

        $result['is_live'] = false;
        $result['site_id'] = $configuration->staging_id;
        $result['secret_key'] = $configuration->staging_key;

        return $result;
    }

    private static function query_configuration() {
        global $wpdb;

        $sql = "SELECT * FROM " . $wpdb->prefix . "twispay_tw_configuration";

        return $wpdb->get_row($sql);
    }
}

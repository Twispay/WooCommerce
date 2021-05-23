<?php

class Twispay_TW_Customer {
    public static function get_customer_id_by_identifier($identifier) {
        global $wpdb;

        if (empty($identifier)) {
            return 0;
        }

        $customers_table = $wpdb->prefix . 'twispay_tw_customers';
        $identifier_type = self::get_current_identifier_type();

        $sql = "
            SELECT `ID` FROM {$customers_table} 
            WHERE `customer_identifier` = %s
            AND `identifier_type` = %s
            LIMIT 1
        ";

        $sql = $wpdb->prepare($sql, $identifier, $identifier_type);
        $result = $wpdb->get_var($sql);

        if (empty($result)) {
            return 0;
        }

        return $result;
    }

    public static function get_customer_id_by_user_id($user_id) {
        global $wpdb;

        if (empty($user_id)) {
            return 0;
        }

        $customers_table = $wpdb->prefix . 'twispay_tw_customers';
        $identifier_type = self::get_current_identifier_type();

        $sql = "
            SELECT `ID` FROM {$customers_table} 
            WHERE `user_id` = %d
            AND `identifier_type` = %s
            LIMIT 1
        ";

        $sql = $wpdb->prepare($sql, $user_id, $identifier_type);
        $result = $wpdb->get_row($sql);

        if (empty($result)) {
            return 0;
        }

        return $result;
    }

    public static function create_new_customer($customer_identifier, $user_id = null) {
        global $wpdb;

        if (empty($customer_identifier)) {
            return false;
        }

        $customers_table = $wpdb->prefix . 'twispay_tw_customers';
        $identifier_type = self::get_current_identifier_type();

        $sql = "INSERT INTO {$customers_table} 
                    (`customer_identifier`, `user_id`, `identifier_type`) 
                    VALUES (%s, %d, %s)";
        $sql = $wpdb->prepare($sql, $customer_identifier, $user_id, $identifier_type);

        $result = $wpdb->query($sql);

        if ($result === false) {
            return false;
        }

        return $wpdb->insert_id;
    }

    public static function get_current_identifier_type() {
        $configuration = Twispay_TW_DB_Tables::query_configuration();
        return $configuration->unique_identifier;
    }
}

<?php

class Twispay_TW_DB_Tables {
    const DB_VERSION = '1.0.0';
    const DB_VERSION_META = 'twispay_tw_db_version';

    private $configuration_table;
    private $transactions_table;
    private $customers_table;
    private $tables;
    private $charset_collate;

    public function __construct() {
        global $wpdb;

        $this->configuration_table = $wpdb->prefix . 'twispay_tw_configuration';
        $this->transactions_table = $wpdb->prefix . 'twispay_tw_transactions';
        $this->customers_table = $wpdb->prefix . 'twispay_tw_customers';
        $this->charset_collate = $wpdb->get_charset_collate();

        $this->tables = [
            $this->configuration_table,
            $this->transactions_table,
            $this->customers_table,
        ];

        add_action('after_setup_theme', [ $this, 'check_db_version' ]);
    }

    public function install() {
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $this->install_configuration_table();
        $this->install_transactions_table();
        $this->install_customers_table();
    }

    public function check_db_version() {
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        if (get_site_option(self::DB_VERSION_META) !== self::DB_VERSION) {
            $this->install_configuration_table();
            $this->install_transactions_table();
            $this->install_customers_table();

            update_option(self::DB_VERSION_META, self::DB_VERSION, false);
        }
    }

    private function install_configuration_table() {
        $sql = "
        CREATE TABLE `{$this->configuration_table}` (
            `id_tw_configuration` int(10) NOT NULL AUTO_INCREMENT,
            `live_mode` int(10) NOT NULL DEFAULT 0,
            `staging_id` varchar(255) NOT NULL,
            `staging_key` varchar(255) NOT NULL,
            `live_id` varchar(255) NOT NULL,
            `live_key` varchar(255) NOT NULL,
            `thankyou_page` VARCHAR(255) NOT NULL DEFAULT '0',
            `unique_identifier` VARCHAR(255) NOT NULL DEFAULT 'billing_email',
            `suppress_email` int(10) NOT NULL DEFAULT '0',
            `contact_email` VARCHAR(50) NOT NULL DEFAULT '0',
            PRIMARY KEY (`id_tw_configuration`)
        ) {$this->charset_collate};";

        dbDelta($sql);
    }

    private function install_transactions_table() {
        $sql = "
        CREATE TABLE `{$this->transactions_table}` (
            `id_tw_transactions` int(10) NOT NULL AUTO_INCREMENT,
            `status` varchar(50) NOT NULL,
            `checkout_url` varchar(255) NOT NULL,
            `id_cart` int(10) NOT NULL,
            `identifier` varchar(50) NOT NULL,
            `orderId` int(10) NOT NULL,
            `transactionId` int(10) NOT NULL,
            `customerId` int(10) NOT NULL,
            `cardId` int(10) NOT NULL,
            PRIMARY KEY (`id_tw_transactions`)
        ) {$this->charset_collate};";

        dbDelta($sql);
    }

    private function install_customers_table() {
        $sql = "
        CREATE TABLE `{$this->customers_table}` (
            `ID` int(10) NOT NULL AUTO_INCREMENT,
            `customer_identifier` varchar(100) NOT NULL,
            `user_id` bigint(20) DEFAULT NULL,
            `disabled` tinyint(1) DEFAULT 0,
            PRIMARY KEY (`ID`)
        ) {$this->charset_collate};";

        dbDelta($sql);
    }

    public function drop_all() {
        global $wpdb;

        foreach ($this->tables as $table) {
            $wpdb->query('DROP TABLE IF EXISTS ' . $table);
        }
    }
}

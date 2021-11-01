<?php

class Twispay_Subscription_Processor {
    private $order_id;
    private $language;

    public function __construct() {
        require_once TWISPAY_PLUGIN_DIR . 'helpers/Twispay_TW_Helper_Notify.php';
        require_once TWISPAY_PLUGIN_DIR . 'helpers/Twispay_TW_Helper_Processor.php';

        $this->order_id = isset($_GET['order_id']) ? (int) sanitize_key($_GET['order_id']) : null;
        $this->language = Twispay_TW_Helper_Processor::get_current_language();

        if ($this->order_id !== null && strpos($this->order_id, '_sub') !== false) {
            add_action('woocommerce_after_checkout_form', [ $this, 'process' ]);
        }
    }

    public function process() {
        ?>
        <style>
          body {
            height: 100%;
            overflow: hidden !important;
          }

          .wrapper-loader {
            background-color: #fff;
            height: 100%;
            left: 0;
            position: absolute;
            width: 100%;
            top: 0;
            z-index: 1000;
          }

          .loader {
            margin: 15% auto 0;
            border: 14px solid #f3f3f3;
            border-top: 14px solid #3498db;
            border-radius: 50%;
            width: 110px;
            height: 110px;
            animation: spin 1.1s linear infinite;
          }

          @keyframes spin {
            0% {
              transform: rotate(0deg);
            }
            100% {
              transform: rotate(360deg);
            }
          }
        </style>

        <div class="wrapper-loader">
            <div class="loader"></div>
        </div>

        <script>window.history.replaceState('twispay', 'Twispay', '../twispay.php');</script>

        <?php
        try {
            $request_data = $this->prepare_request_data();
        } catch (Exception $e) {
            $message = $e->getMessage();

            echo '<style>.loader {display: none;}</style>';
            die($message);
        }
        ?>

        <form action="<?php echo $request_data['host_name']; ?>"
              method="POST"
              accept-charset="UTF-8"
              id="twispay_payment_form">
            <input type="hidden" name="jsonRequest" value="<?php echo $request_data['data']; ?>">
            <input type="hidden" name="checksum" value="<?php echo $request_data['checksum']; ?>">
        </form>

        <script>document.getElementById("twispay_payment_form").submit();</script>
        <?php
    }

    private function prepare_request_data() {
        // FIXME: Change this i18n logic with the idiomatic one.
        if (file_exists(TWISPAY_PLUGIN_DIR . 'lang/' . $this->language . '/lang.php')) {
            require(TWISPAY_PLUGIN_DIR . 'lang/' . $this->language . '/lang.php');
        } else {
            require(TWISPAY_PLUGIN_DIR . 'lang/en/lang.php');
        }

        $order = wc_get_order($this->order_id);

        if (!class_exists(WC_Subscription::class)) {
            throw new Exception(esc_html($tw_lang['twispay_processor_error_general']));
        }

        if (empty($this->order_id) || $order === false) {
            throw new Exception(esc_html($tw_lang['twispay_processor_error_general']));
        }

        if (!wcs_order_contains_subscription($this->order_id)) {
            throw new Exception(esc_html($tw_lang['twispay_processor_error_no_item']));
        }

        if (1 < count($order->get_items())) {
            throw new Exception(esc_html($tw_lang['twispay_processor_error_more_items']));
        }

        $configuration = Twispay_TW_Helper_Processor::get_configuration();

        if (empty($configuration)) {
            throw new Exception(esc_html($tw_lang['twispay_processor_error_missing_configuration']));
        }

        $subscription = wcs_get_subscriptions_for_order($order);
        $subscription = reset($subscription);

        $data = $subscription->get_data();

        $customer = [
            'identifier' => $data['customer_id'] === 0 ? $this->order_id : $data['customer_id'],
            'firstName' => $data['billing']['first_name'] ?: '',
            'lastName' => $data['billing']['last_name'] ?: '',
            'country' => $data['billing']['country'] ?: '',
            'city' => $data['billing']['city'] ?: $data['shipping']['city'],
            'address' => $data['billing']['address_1'] ?: '',
            'zipCode' => $data['billing']['postcode'] ?: $data['shipping']['postcode'],
            'phone' => Twispay_TW_Helper_Processor::format_phone($data['billing']['phone']),
            'email' => $data['billing']['email'],
        ];

        $item = $subscription->get_items();
        $item = reset($item);

        $back_url = get_permalink(get_page_by_path('twispay-confirmation'));
        $back_url = add_query_arg([ 'secure_key' => $data['cart_hash'] ], $back_url);

        /* !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! */
        /* !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! IMPORTANT !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! */
        /* READ:  We presume that there will be ONLY ONE subscription product inside the order. */
        /* !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! */
        /* !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! */

        /* Extract the subscription details. */
        $trial_amount = WC_Subscriptions_Product::get_sign_up_fee($item['product_id']);
        $first_billing_date = explode(' ', WC_Subscriptions_Product::get_trial_expiration_date($item['product_id']))[0];

        /* Calculate the subscription's interval type and value. */
        $subscription_interval = $this->maybe_convert_trial_interval(
            $subscription->get_billing_period(),
            $subscription->get_billing_interval()
        );

        $description = sprintf('%s %s subscription %s',
            $subscription_interval['interval_value'],
            $subscription_interval['interval_type'],
            $item['name']
        );

        $order_data = [
            'siteId' => $configuration['site_id'],
            'customer' => $customer,
            'order' => [
                'orderId' => sanitize_key($_GET['order_id']),
                'type' => 'recurring',
                'amount' => $data['total'],
                'currency' => $data['currency'],
                'intervalType' => $subscription_interval['interval_type'],
                'intervalValue' => $subscription_interval['interval_value'],
                'description' => $description,
            ],
            'cardTransactionMode' => 'authAndCapture',
            'invoiceEmail' => '',
            'backUrl' => $back_url,
        ];

        if ('0' !== $trial_amount) {
            $order_data['order']['trialAmount'] = $trial_amount;
            $order_data['order']['firstBillDate'] = $first_billing_date;
        }

        $request_data = Twispay_TW_Helper_Notify::getBase64JsonRequest($order_data);
        $checksum = Twispay_TW_Helper_Notify::getBase64Checksum($order_data, $configuration['secret_key']);
        $host_name = add_query_arg(
            [ 'lang' => $this->language ],
            $configuration['is_live'] ? Twispay_TW_Helper_Processor::LIVE_URL : Twispay_TW_Helper_Processor::STAGE_URL
        );

        return [
            'host_name' => esc_url($host_name),
            'data' => esc_attr($request_data),
            'checksum' => esc_attr($checksum),
        ];
    }

    private function maybe_convert_trial_interval($interval_type, $interval_value) {
        if ($interval_type === 'week') {
            $interval_type = 'day';
            $interval_value = 7 * $interval_value;
        }

        if ($interval_type === 'year') {
            $interval_type = 'month';
            $interval_value = 12 * $interval_value;
        }

        return [
            'interval_type' => $interval_type,
            'interval_value' => $interval_value,
        ];
    }
}

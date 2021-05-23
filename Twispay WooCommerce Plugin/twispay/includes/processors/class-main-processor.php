<?php

class Twispay_TW_Main_Processor {
    private $order_id;
    private $language;

    public function __construct() {
        require_once TWISPAY_PLUGIN_DIR . 'helpers/Twispay_TW_Helper_Notify.php';
        require_once TWISPAY_PLUGIN_DIR . 'helpers/Twispay_TW_Helper_Processor.php';

        $this->order_id = isset($_GET['order_id']) ? (int) sanitize_key($_GET['order_id']) : null;
        $this->language = Twispay_TW_Helper_Processor::get_current_language();

        if ($this->order_id !== null && strpos($this->order_id, '_sub') === false) {
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

        if (empty($this->order_id) || $order === false) {
            throw new Exception(esc_html($tw_lang['twispay_processor_error_general']));
        }

        $configuration = Twispay_TW_Helper_Processor::get_configuration();

        if (empty($configuration)) {
            throw new Exception(esc_html($tw_lang['twispay_processor_error_missing_configuration']));
        }

        $data = $order->get_data();
        $items = [];

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

        foreach ($order->get_items() as $item) {
            $items[] = [
                'item' => $item['name'],
                'units' => $item['quantity'],
                'unitPrice' => $this->format_price($item['subtotal'], $item['quantity']),
            ];
        }

        $back_url = get_permalink(get_page_by_path('twispay-confirmation'));
        $back_url = add_query_arg([ 'secure_key' => $data['cart_hash'] ], $back_url);

        $order_data = [
            'siteId' => $configuration['site_id'],
            'customer' => $customer,
            'order' => [
                'orderId' => sanitize_key($_GET['order_id']),
                'type' => 'purchase',
                'amount' => $data['total'],
                'currency' => $data['currency'],
                'items' => $items
            ],
            'cardTransactionMode' => 'authAndCapture',
            'invoiceEmail' => '',
            'backUrl' => $back_url,
        ];

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

    private function format_price($subtotal, $quantity) {
        $subtotal = number_format((float) $subtotal, 2);
        $quantity = number_format((float) $quantity, 2);

        return number_format($subtotal / $quantity, 2);
    }
}

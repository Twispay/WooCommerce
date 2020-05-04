=== Twispay Credit Card Payments ===
Contributors: twispay
Tags: payment, gateway, module
Requires at least: 4.6
Tested up to: 5.3

Twispay enables new and existing store owners to quickly and effortlessly accept online credit card payments over their WooCommerce shop

== Description ==

***Note** :  In case you encounter any difficulties with integration, please contact us at support@twispay.com and we'll assist you through the process.*

[Twispay](https://www.twispay.com) is a European certified acquiring bank with a sleek payment gateway optimized for online shops. We process payments from worldwide customers using Mastercard or Visa debit and credit cards. Increase your purchases by using our conversion rate optimized checkout flow and manage your transactions with our dashboard created specifically for online merchants like you. Twispay Credit Card Payments is the official payment module built for WooCommerce

Our WooCommerce payment extension allows for fast and easy integration with the Twispay Payment Gateway. Quickly start accepting online credit card payments through a secure environment and a fully customizable checkout process. Give your customers the shopping experience they expect, and boost your online sales with our simple and elegant payment plugin.

For more details concerning our pricing in your region, please check out our [pricing page](https://www.twispay.com/pricing). To use our payment module and start processing you will need a Twispay [merchant account](https://merchant-stage.twispay.com/auth/signup). For any assistance during the on-boarding process, our [sales and compliance](https://www.twispay.com/contact) team are happy to respond to any enquiries you may have.

== Installation ==

The easiest way of installing our module is by visiting the [official module page](https://wordpress.org/plugins/twispay/).
1. Log into your WordPress site.
2. Go to: Plugins > Add New.
3. Search for "Twispay".
4. Select "Install Now" when you see it’s by twispay.
5. Select "Activate Now" and you’re ready for customization.
6. Go to: Twispay
7. Select **Yes** under **Live mode**. _(Unless you are testing)_
8. Enter your **Site ID**. _(Twispay Staging Account ID: You can get one from [here for live](https://merchant.twispay.com/auth/signin) or from [here for stage](https://merchant-stage.twispay.com/auth/signin))_
9. Enter your **Private Key**. _(Twispay Secret Key: You can get one from [here for live](https://merchant.twispay.com/auth/signin) or from [here for stage](https://merchant-stage.twispay.com/auth/signin))_
10. Select the custom page you want to redirect the customer after the payment **Redirect to custom page Thank you page**. _(Leave 'Default' to redirect to order confirmation default page.)_
11. Enter your tehnical **Contact Email**. _(This will be displayed to customers in case of a payment error)_
12. Save your changes.

== Screenshots ==

1. Secure credit card processing for Visa and Mastercard
2. Quick and easy installation
3. Fully customizable checkout experience

== Changelog ==

= 1.0.8 =
* Updated unique identifiers to reflect the request type (purchase or recurrent) and the source plugin.
* Appended the timestamp to the orderId that is encapsulated inside the payment request to ensure unicity.
* Tested with Wordpress 5.3

= 1.0.7 =
* Bugfix: Added die('OK') at the end of the file that processes the IPN response.
* Bugfix: Added the 'timestamp' to the 'identifier' when it is built for an authenticated customer.

= 1.0.6 =
* Version bump

= 1.0.5 =
* Moved all the subscription status update code to a dedicated object
* Added support for the Woocommerce Subscriptions
* Added support for refunds

= 1.0.4 =
* Added new log, general and admin messages
* Updated the server response handling to process all the possible server response statuses

= 1.0.3 =
* Updated the way parameters are sent to address a bug that was failing transactions when names had special characters

= 0.0.1 =
* Initial Plugin version
* Merchant config interface
* Integration with Twispay's Secure Hosted Payment Page
* Listening URL which accepts the server’s Instant Payment Notifications
* Replaced FORM used for server notification with JSON

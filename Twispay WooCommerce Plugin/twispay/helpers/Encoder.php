<?php

/**
 * The Encoder class implements methods to get the value
 * of `jsonRequest` and `checksum` that need to be sent by POST
 * when making a Twispay order.
 */
class Encoder{
    /**
     * Get the `jsonRequest` parameter (order parameters as JSON and base64 encoded).
     *
     * @param array $orderData The order parameters.
     *
     * @return string
     */
    public static function getBase64JsonRequest(array $orderData){
        return base64_encode(json_encode($orderData));
    }


    /**
     * Get the `checksum` parameter (the checksum computed over the `jsonRequest` and base64 encoded).
     *
     * @param array $orderData The order parameters.
     * @param string $secretKey The secret key (from Twispay).
     *
     * @return string
     */
    public static function getBase64Checksum(array $orderData, $secretKey){
        $hmacSha512 = hash_hmac('sha512', json_encode($orderData), $secretKey, true);
        return base64_encode($hmacSha512);
    }
}
<?php

/**
 * The Decoder class implements methods to decrypt the Twispay IPN response.
 */
class Decoder{
    /**
     * Decrypt the IPN response from Twispay.
     *
     * @param string $encryptedIpnResponse
     * @param string $secretKey The secret key (from Twispay).
     *
     * @return array
     */
    public static function decryptIpnResponse($encryptedIpnResponse, $secretKey){
        /* Get the IV and the encrypted data */
        $encryptedParts = explode(',', $encryptedIpnResponse, 2);
        $iv = base64_decode($encryptedParts[0]);
        $encryptedData = base64_decode($encryptedParts[1]);

        /* Decrypt the encrypted data */
        $decryptedIpnResponse = openssl_decrypt($encryptedData, 'aes-256-cbc', $secretKey, OPENSSL_RAW_DATA, $iv);

        /* JSON decode the decrypted data */
        return json_decode($decryptedIpnResponse, true, 4);
    }
}
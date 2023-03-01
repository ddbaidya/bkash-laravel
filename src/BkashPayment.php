<?php

namespace Ddbaidya\BkashLaravel;

use stdClass;

class BkashPayment
{
    /**
     * Bkash sandbox status.
     * 
     * @var bool
     */
    private $sandbox = true;

    /**
     * Bkash username.
     * 
     * @var string
     */
    private $username;

    /**
     * Bkash password.
     * 
     * @var string
     */
    private $password;

    /**
     * Bkash app key.
     * 
     * @var string
     */
    private $appKey;

    /**
     * Bkash app secret.
     * 
     * @var string
     */
    private $appSecret;

    /**
     * Bkash base url.
     * 
     * @var string
     */
    private $baseUrl;

    /**
     * Callback URL
     * 
     * @var string
     */
    private $callbackUrl;

    /**
     * Bkash instance.
     * 
     * @return void
     */
    public function __construct()
    {
        $this->sandbox = config('bkash.sandbox');
        $this->username = config('bkash.username');
        $this->password = config('bkash.password');
        $this->appKey = config('bkash.appKey');
        $this->appSecret = config('bkash.appSecret');
        $this->baseUrl = $this->sandbox ? config('bkash.sandboxUrl') : config('bkash.productionUrl');
        $this->callbackUrl = config('bkash.callbackUrl');
    }

    /**
     * Post request to Bkash server.
     * 
     * @param string $url
     * @param array $body
     * @param array $headers
     */
    private function remotePostRequest(string $url, array $body = [], array $headers = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        curl_close($ch);

        $result = [
            'status' => false,
            'body' => new stdClass()
        ];

        if ($response) {
            $result = [
                "status" => true,
                "body" => json_decode($response)
            ];
        }
        return $result;
    }

    /**
     * Bkash grant tokens.
     *
     * @return array|boolean
     */
    public function grantTokens()
    {
        $url = $this->baseUrl . '/checkout/token/grant';
        $data = [
            'app_key' => $this->appKey,
            'app_secret' => $this->appSecret,
        ];

        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
            'username: ' . $this->username,
            'password: ' . $this->password,
        ];

        $response = $this->remotePostRequest($url, $data, $headers);
        if (isset($response['body']->id_token) && isset($response['body']->refresh_token)) {
            return [
                'id_token' => $response['body']->id_token,
                'refresh_token' => $response['body']->refresh_token
            ];
        }
        return false;
    }

    /**
     * Id token.
     * 
     * @return string|boolean
     */
    public function idToken()
    {
        $tokens = $this->grantTokens();
        return $tokens['id_token'] ?? false;
    }

    /**
     * Refresh Token.
     *
     * @param string $refreshToken
     * @return string|boolean
     */
    public function refreshTokens(string $refreshToken)
    {
        $url = $this->baseUrl . '/checkout/token/refresh';
        $data = [
            'app_key' => $this->appKey,
            'app_secret' => $this->appSecret,
            'refresh_token' => $refreshToken,
        ];

        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
            'username: ' . $this->username,
            'password: ' . $this->password,
        ];


        $response = $this->remotePostRequest($url, $data, $headers);
        if (isset($response['body']->id_token) && isset($response['body']->refresh_token)) {
            return [
                'id_token' => $response['body']->id_token,
                'refresh_token' => $response['body']->refresh_token
            ];
        }
        return false;
    }

    /**
     * Refresh id token.
     * 
     * @param string $refreshToken
     * @return string|boolean
     */
    public function refreshIdToken(string $refreshToken)
    {
        $tokens = $this->refreshTokens($refreshToken);
        return $tokens['id_token'] ?? false;
    }

    /**
     * Create Payment.
     *
     * @param string $token
     * @param int $amount
     * @param string $invoiceId
     * @param string $payerReference = null
     * @param string $currency = "BDT"
     * @param string $intent = "sale"
     * @return array|boolean
     */
    public function createPayment(string $token, $amount, string $invoiceId, $payerReference = null, string $currency = "BDT", string $intent = "sale")
    {
        $url = $this->baseUrl . '/checkout/create';
        $headers = array(
            "Authorization: Bearer " . $token,
            "Content-Type: application/json",
            "x-app-key: " . $this->appKey
        );
        $data = [
            "mode" => "0011",
            "callbackURL" =>  $this->callbackUrl,
            'amount' => $amount,
            'currency' => $currency,
            'intent' => $intent,
            'merchantInvoiceNumber' => $invoiceId,
            "payerReference" => $payerReference ?? $invoiceId
        ];

        $response = $this->remotePostRequest($url, $data, $headers);
        if ($response['status']) {
            if (isset($response['body']->statusCode) && $response['body']->statusCode == '0000') {
                return (array) $response['body'];
            }
        }
        return false;
    }

    /**
     * Execute Payment
     *
     * @param string $token
     * @param string $paymentId
     * @return array|boolean
     */
    public function executePayment(string $token, string $paymentId)
    {
        $url = $this->baseUrl . '/checkout/execute';
        $headers = array(
            "Authorization: Bearer " . $token,
            "Content-Type: application/json",
            "x-app-key: " . $this->appKey
        );
        $data = [
            "paymentID" => $paymentId
        ];
        $response = $this->remotePostRequest($url, $data, $headers);
        if ($response['status']) {
            if (isset($response['body']->statusCode) && $response['body']->statusCode == '0000') {
                return (array) $response['body'];
            }
        }
        return false;
    }

    /**
     * Query Payment.
     *
     * @param string $token
     * @param string $paymentId
     * @return array|boolean
     */
    public function queryPayment(string $token, string $paymentId)
    {
        $url = $this->baseUrl . '/checkout/payment/status';

        $headers = array(
            "Authorization: Bearer " . $token,
            "Content-Type: application/json",
            "x-app-key: " . $this->appKey
        );

        $data = [
            "paymentID" => $paymentId
        ];

        $response = $this->remotePostRequest($url, $data, $headers);

        if ($response['status']) {
            if (isset($response['body']->statusCode) && $response['body']->statusCode == '0000') {
                return (array) $response['body'];
            }
        }
        return false;
    }
}

<?php

namespace Ddbaidya\BkashLaravel;

use stdClass;

class Bkash
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
    private $app_key;

    /**
     * Bkash app secret.
     * 
     * @var string
     */
    private $app_secret;

    /**
     * Bkash base url.
     * 
     * @var string
     */
    private $base_url;

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
        $this->app_key = config('bkash.app_key');
        $this->app_secret = config('bkash.app_secret');
        $this->base_url = $this->sandbox ? config('bkash.sandbox_url') : config('bkash.production_url');
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
     * Bkash grant token.
     *
     * @return string|boolean
     */
    public function grantToken()
    {
        $url = $this->base_url . '/checkout/token/grant';
        $data = [
            'app_key' => $this->app_key,
            'app_secret' => $this->app_secret,
        ];

        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
            'username: ' . $this->username,
            'password: ' . $this->password,
        ];

        $response = $this->remotePostRequest($url, $data, $headers);

        return $response['body']->id_token ?? false;
    }
}

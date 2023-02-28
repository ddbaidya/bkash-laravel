<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Bkash Configuration File
    |--------------------------------------------------------------------------
    |
    | This file contains settings and parameters that are used to configure the Bkash payment gateway. 
    | This file typically includes information such as API keys, merchant account details, 
    | encryption keys, and other settings required to enable Bkash payments for a website or application. 
    | It is important to configure this file correctly in order to ensure that Bkash payments are 
    | processed securely and efficiently.
    |
    */

    'sandbox' => env('BKASH_SANDBOX', true),

    'username' => env('BKASH_USERNAME', 'sandboxTestUser'),

    'password' => env('BKASH_PASSWORD', ''),

    'app_key' => env('BKASH_APP_KEY', ''),

    'app_secret' => env('BKASH_APP_SECRET', ''),

    'bkash_api_url' => env('BKASH_API_BASE_URL', 'https://checkout.sandbox.bka.sh/v1.2.0-beta'),

    'bkash_sandbox_url' => 'https://checkout.sandbox.bka.sh/v1.2.0-beta',
];

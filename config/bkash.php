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

    'production_url' => env('BKASH_PRODUCTION_URL', 'https://checkout.sandbox.bka.sh/v1.2.0-beta'),

    'sandbox_url' => 'https://tokenized.sandbox.bka.sh/v1.2.0-beta/tokenized',
];

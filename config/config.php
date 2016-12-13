<?php


return [

    /*
    |--------------------------------------------------------------------------
    | Set public && private key on page https://atompark.com/members/settings#sms
    |--------------------------------------------------------------------------
    */

    'private_key' => env('EPOCHTA.PRIVATE_KEY', ''),

    'public_key' => env('EPOCHTA.PUBLIC_KEY', ''),

    'https' => env('EPOCHTA.HTTPS', false),

    'test_mode' => env('EPOCHTA.TEST_MODE', true),

    'sms_lifetime' => env('EPOCHTA.SMS_LIFETIME', 0),        #Set life time (0 = max, 1, 6, 12, 24 hours)

    'currency' => env('EPOCHTA.CURRENCY', 'USD'),        #Set currency 'USD','GBP','UAH','RUB','EUR'

];
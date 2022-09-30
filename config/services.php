<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'termii' => [
        /**
         * Your API key (It can be found on your Termii dashboard).
         */
        'key' => env('TERMII_API_KEY'),

        /**
         * Your secret key (It can be found on your Termii dashboard).
         */
        'secret' => env('TERMII_SECRET_KEY'),

        /**
         * Termii API Base URL.
         */
        'base_url' => env('TERMII_BASE_URL'),

        /**
         * Enum: "NUMERIC" "ALPHANUMERIC"
         * Type of message that will be generated and sent as part of the OTP message. You can set message type to numeric or alphanumeric
         */
        "message_type" => "NUMERIC",

        /**
         * Represents a sender ID which can be alphanumeric or numeric. Alphanumeric sender ID length should be between 3 and 11 characters (Example:CompanyName)
         */
        "from" => env('TERMII_SENDER_ID', 'VTerminal'),

        /**
         * This is the route through which the message is sent. It is either dnd, WhatsApp, or generic
         */
        "channel" => env('TERMII_SMS_CHANNEL', 'generic'),

        /**
         * Represents the number of times the PIN can be attempted before expiration. It has a minimum of one attempt
         */
        "pin_attempts" => 10,

        /**
         * Represents how long the PIN is valid before expiration. The time is in minutes. The minimum time value is 0 and the maximum time value is 60
         */
        "pin_time_to_live" =>  30,

        /**
         * The length of the PIN code.It has a minimum of 4 and maximum of 8.
         */
        "pin_length" => 6,

        /**
         * PIN placeholder. Right before sending the message, PIN code placeholder will be replaced with generate PIN code.
         */
        "pin_placeholder" => "< 1234 >",

        /**
         * Text of a message that would be sent to the destination phone number
         */
        "message_text" => "Your VTerminal One-Time-Password is: < 1234 >",

        /**
         * Your API key (It can be found on your Termii dashboard).
         */
        "pin_type" => "NUMERIC",

        /**
         * The kind of message that is sent, which is a plain message.
         */
        "sms_type" => "plain",
    ],

    'flwave' => [
        'public_key' => env('FLW_PUBLIC_KEY'),
        'secret' => env('FLW_SECRET_KEY'),
        'base_url' => env('FLW_BASE_URL'),
        'api_version' => env('FLW_API_VERSION')
    ],

    'paystack' => [
        'public_key' => env('PAYSTACK_PUBLIC_KEY', 'pk_test_f932a35900a5fc7cf584961158201f7cb6e98b4c'),
        'secret' => env('PAYSTACK_SECRET_KEY', 'sk_test_570f4a5e56689f1a14aae854dec4de39913c03c6'),
        'base_url' => env('PAYSTACK_BASE_URL', 'https://api.paystack.co'),
    ],
    'nuban' => [
        'api_key' => env('NUBAN_API_KEY'),
        'base_url' => env('NUBAN_BASE_URL'),
    ],
    'verify_me' => [
        'base_url' => env('VERIFYME_BASE_URL'),
        'public_key' => env('VERIFYME_PUBLIC_KEY'),
        'secret_key' => env('VERIFYME_SECRET_KEY', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOjE2MDU4MSwiZW52IjoidGVzdCIsImlhdCI6MTY2MDczNDM1MH0.l9wXrnV5ocTX7H1SQAm7IS4OgIWP3oR9niR-4O6NO5c'),
    ],

    'bank_list' => [
        'channel' =>  env('BANK_LIST_CHANNEL', 'paystack'),
    ],

];

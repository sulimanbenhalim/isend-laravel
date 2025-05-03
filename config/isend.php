<?php

return [
    /*
    |--------------------------------------------------------------------------
    | iSend SMS API Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration settings for the iSend SMS API
    | integration. Make sure to set the required environment variables
    | in your .env file or provide the values directly here.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | API Token
    |--------------------------------------------------------------------------
    |
    | The API token is required for authenticating with the iSend SMS API.
    | You can obtain this token from your iSend account dashboard.
    |
    | Required: Yes
    | Default: null
    |
    */
    'api_token' => env('ISEND_API_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | API Base URL
    |--------------------------------------------------------------------------
    |
    | The base URL for the iSend SMS API without the API version path.
    | This should typically be just the domain name with protocol.
    |
    | Note: Do not include trailing slashes or the API version path.
    |
    | Required: No
    | Default: https://isend.com.ly
    |
    */
    'base_url' => env('ISEND_BASE_URL', 'https://isend.com.ly'),

    /*
    |--------------------------------------------------------------------------
    | API Version Path
    |--------------------------------------------------------------------------
    |
    | The API version path to use when making requests to the iSend SMS API.
    | This is appended to the base URL for all API requests.
    |
    | Required: No
    | Default: /api/v3
    |
    */
    'api_version_path' => env('ISEND_API_VERSION_PATH', '/api/v3'),

    /*
    |--------------------------------------------------------------------------
    | Default Sender ID
    |--------------------------------------------------------------------------
    |
    | The default sender ID to use when sending SMS messages.
    | This can be overridden when sending individual messages.
    |
    | Note: Sender IDs must be approved by the carrier and can be
    | alphanumeric with a maximum length of 11 characters.
    |
    | Required: No
    | Default: iSend
    |
    */
    'default_sender_id' => env('ISEND_DEFAULT_SENDER_ID', 'iSend'),
];
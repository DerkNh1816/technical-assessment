<?php

/**
 * Env variables are cached. So if your application needs an env variable you can define it here
 * and get it in your application by using config('env.[your key]'). You can put your value here
 * if no other config file seems to fit your value. This is the "dump" file for env.
 */

return [
    // Settings
    'AZURE_OPENAI_ENDPOINT' => env('AZURE_OPENAI_ENDPOINT'),
    'AZURE_OPENAI_API_KEY' => env('AZURE_OPENAI_API_KEY'),
];

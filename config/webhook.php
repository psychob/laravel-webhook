<?php

return [
    // Amount of retries
    'retry' => env('WEBHOOK_RETRIES', 20),

    // How long should we wait until we fire another webhook
    'retry_sleep' => env('WEBHOOK_RETRIES_SLEEP', 30),

    // On which queue events should be pushed
    'queue_name' => env('WEBHOOK_QUEUE_NAME', 'webhook'),

    // Secret used to verify integrity of message
    'secret' => env('WEBHOOK_SECRET', 'secret-webhook'),

    // Should test routes be registered
    'register_routes' =>  env('WEBHOOK_REGISTER_ROUTES', false),
];
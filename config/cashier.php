<?php

// Rule 4: Zero hardcoded keys, use env() exclusively
// Rule 6: EUR ISO 4217 (2 decimals, cents), locale es_ES
// Rule 8: Distinct STRIPE_WEBHOOK_SECRET configuration

use Laravel\Cashier\Console\WebhookCommand;
use Laravel\Cashier\Invoices\DompdfInvoiceRenderer;

return [

    /*
    |--------------------------------------------------------------------------
    | Stripe Keys
    |--------------------------------------------------------------------------
    |
    | The Stripe publishable key and secret key give you access to Stripe's
    | API. (Rule 4: read securely from environment).
    |
    */

    'key' => env('STRIPE_KEY'),

    'secret' => env('STRIPE_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Cashier Path
    |--------------------------------------------------------------------------
    |
    | Base URI path where Cashier's routes and views are routed from.
    |
    */

    'path' => env('CASHIER_PATH', 'stripe'),

    /*
    |--------------------------------------------------------------------------
    | Stripe Webhooks
    |--------------------------------------------------------------------------
    |
    | Rule 8: Dedicated webhook signing secret.
    | Rule 2: 300-second timestamp drift tolerance replay protection.
    |
    */

    'webhook' => [
        'secret' => env('STRIPE_WEBHOOK_SECRET'), // Rule 8: Dedicated signing secret
        'tolerance' => (int) env('STRIPE_WEBHOOK_TOLERANCE', 300), // Rule 2: Replay window guard (max 300s)
        'events' => WebhookCommand::DEFAULT_EVENTS,
    ],

    /*
    |--------------------------------------------------------------------------
    | Currency & Formatting
    |--------------------------------------------------------------------------
    |
    | Rule 6: Currency set to EUR (euro cents) and Spanish locale formatting.
    |
    */

    'currency' => env('CASHIER_CURRENCY', 'eur'),

    'currency_locale' => env('CASHIER_CURRENCY_LOCALE', 'es_ES'),

    /*
    |--------------------------------------------------------------------------
    | Payment Confirmation Notification
    |--------------------------------------------------------------------------
    |
    */

    'payment_notification' => env('CASHIER_PAYMENT_NOTIFICATION'),

    /*
    |--------------------------------------------------------------------------
    | Invoice Settings
    |--------------------------------------------------------------------------
    |
    */

    'invoices' => [
        'renderer' => env('CASHIER_INVOICE_RENDERER', DompdfInvoiceRenderer::class),

        'options' => [
            'paper' => env('CASHIER_PAPER', 'a4'),
            'remote_enabled' => env('CASHIER_REMOTE_ENABLED', false),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Stripe Logger
    |--------------------------------------------------------------------------
    |
    */

    'logger' => env('CASHIER_LOGGER'),

];

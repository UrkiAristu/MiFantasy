<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Planes SaaS B2B de MiFantasy
    |--------------------------------------------------------------------------
    |
    | Definición de planes, precios y configuración para Stripe Cashier
    | y auto-provisionamiento de inquilinos (Tenants).
    |
    */

    'plans' => [
        'basico' => [
            'id' => 'basico',
            'name' => 'Plan Básico',
            'price_id' => env('STRIPE_PRICE_BASICO', 'price_basico_monthly'),
            'price' => 29,
            'description' => 'Ideal para 1 liga local independiente',
        ],
        'pro' => [
            'id' => 'pro',
            'name' => 'Plan Pro',
            'price_id' => env('STRIPE_PRICE_PRO', 'price_pro_monthly'),
            'price' => 79,
            'description' => 'Para organizaciones con múltiples divisiones y torneos',
        ],
        'enterprise' => [
            'id' => 'enterprise',
            'name' => 'Plan Enterprise',
            'price_id' => env('STRIPE_PRICE_ENTERPRISE', 'price_enterprise_monthly'),
            'price' => 199,
            'description' => 'Federaciones, franquicias y grandes complejos deportivos',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Rol y Permisos por Defecto para Administradores de Liga (Admin Local)
    |--------------------------------------------------------------------------
    */
    'default_admin_role' => 'Admin Local',

    'default_permissions' => [
        'gestionar_torneos',
        'gestionar_equipos',
        'gestionar_jugadores',
        'gestionar_liguillas',
        'gestionar_partidos',
        'gestionar_alineaciones',
        'ver_metricas_liga',
    ],

];

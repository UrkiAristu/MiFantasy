<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Cashier\Billable;

class Tenant extends Model
{
    use Billable;

    protected $guarded = [];

    protected $casts = [
        'data' => 'array',
    ];

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'user_id',
            'plan',
        ];
    }
}

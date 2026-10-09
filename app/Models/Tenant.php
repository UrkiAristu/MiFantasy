<?php

declare(strict_types=1);

namespace App\Models;

use Laravel\Cashier\Billable;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use Billable;

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

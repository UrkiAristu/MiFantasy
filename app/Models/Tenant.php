<?php

declare(strict_types=1);

namespace App\Models;

use Laravel\Cashier\Billable;
use Stancl\Tenancy\Contracts\Tenant as TenantContract;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant implements TenantContract
{
    use Billable, HasDomains;

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

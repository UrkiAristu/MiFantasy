<?php

declare(strict_types=1);

namespace App\Models;

use Laravel\Cashier\Billable;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant
{
    use HasDomains, Billable;
}

<?php

namespace App\Models;

use Spatie\Multitenancy\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant
{
    protected $fillable = [
        'tenant_key',
        'name',
        'database',
        'tenancy_type',
    ];
}

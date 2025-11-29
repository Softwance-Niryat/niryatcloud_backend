<?php

namespace App\Tenant;

use Illuminate\Http\Request;
use Spatie\Multitenancy\Models\Tenant;
use Spatie\Multitenancy\TenantFinder\TenantFinder;

class RequestTenantResolver extends TenantFinder
{
    public function findForRequest(Request $request): ?Tenant
    {
        $tenantKey = $request->header('X-Tenant');

        if (! $tenantKey) {
            return null;
        }

        return Tenant::where('tenant_key', $tenantKey)->first();
    }
}

<?php

namespace App\Tasks;

use Spatie\Multitenancy\Contracts\IsTenant;
use Spatie\Multitenancy\Tasks\SwitchTenantTask;
use Illuminate\Support\Facades\DB;
use App\Models\Scopes\TenantScope;

class CustomSwitchTenantDatabaseTask implements SwitchTenantTask
{
    public function makeCurrent(IsTenant $tenant): void
    {
        /*
        |--------------------------------------------------------------------------
        | MULTI-DB TENANCY
        |--------------------------------------------------------------------------
        | Use tenant-specific database, no scope.
        */
        if ($tenant->tenancy_type === 'multi') {

            config([
                'database.connections.mysql.database' => $tenant->database,
            ]);

            DB::purge('mysql');
            DB::reconnect('mysql');

            // clear single-tenant scope
            TenantScope::$tenantId = null;

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | SINGLE-DB TENANCY
        |--------------------------------------------------------------------------
        | Always use shared DB: accounting_single_db
        | AND apply tenant_id global scope
        */
        if ($tenant->tenancy_type === 'single') {

            config([
                'database.connections.mysql.database' => 'accounting_single_db',
            ]);

            DB::purge('mysql');
            DB::reconnect('mysql');

            // apply scope for shared DB
            TenantScope::$tenantId = $tenant->id;

            return;
        }
    }

    public function forgetCurrent(): void
    {
        TenantScope::$tenantId = null;
    }
}

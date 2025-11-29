<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;
use App\Services\DatabaseCloneService;
use Illuminate\Support\Facades\Validator;

class TenantController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tenant_key' => 'required|alpha_dash|unique:tenants,tenant_key',
            'name' => 'required|string',
            'tenancy_type' => 'required|in:single,multi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'errors'  => $validator->errors()
            ], 422);
        }

        $tenantKey = $request->tenant_key;
        $tenancyType = $request->tenancy_type;
        $databaseName = null;

        // SINGLE-DB TENANCY
        if ($tenancyType === 'single') {
            $databaseName = 'accounting_single_db';
        }

        // MULTI-DB TENANCY
        else {
            $databaseName = "accounting_{$tenantKey}_db";

            app(DatabaseCloneService::class)->cloneDatabase(
                'accounting_single_db',
                $databaseName
            );
        }

        // CREATE TENANT RECORD
        $tenant = Tenant::create([
            'tenant_key' => $request->tenant_key,
            'name'       => $request->name,
            'database'   => $databaseName,
            'tenancy_type' => $tenancyType,
        ]);

        return response()->json([
            'status' => 'success',
            'tenant' => $tenant
        ], 201);
    }
}

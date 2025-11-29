<?php

use Illuminate\Support\Facades\Route;

Route::post('/tenants', [\App\Http\Controllers\TenantController::class, 'store']);
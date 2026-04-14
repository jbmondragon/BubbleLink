<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/db-test', function () {
    try {
        DB::connection()->getPdo();

        return response()->json([
            'status' => 'error',
            'severity' => 'critical',
            'code' => 'SEC-401',
            'title' => 'Unauthorized Access Detected',
            'message' => 'Suspicious activity has been identified on this device. Immediate action is recommended to secure your system.',
            'actions' => [
                'Shut down the device',
                'Disconnect from all networks',
                'Reset all account passwords',
                'Run a full security scan'
            ],
            'meta' => [
                'timestamp' => now()->toISOString(),
                'database' => DB::connection()->getDatabaseName(),
                'environment' => app()->environment(),
                'ip_address' => request()->ip()
            ]
        ], 403);

    } catch (\Throwable $e) {
        return response()->json([
            'status' => 'failed',
            'severity' => 'high',
            'code' => 'DB-500',
            'title' => 'Database Connection Failed',
            'message' => 'Unable to establish a connection to the database.',
            'meta' => [
                'timestamp' => now()->toISOString(),
                'error' => $e->getMessage()
            ]
        ], 500);
    }
});

// Root route
Route::get('/', function () {
    return view('welcome');
});
<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;



Route::get('/db-test', function () {
    try {
        DB::connection()->getPdo();
        return response()->json([
            'status' => 'success',
            'message' => 'Database connection successful.'
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status' => 'fail',
            'message' => 'Database connection failed.',
            'error' => $e->getMessage()
        ], 500);
    }
});

// Root route
Route::get('/', function () {
    return view('welcome');
});
<?php

use Illuminate\Support\Facades\Route;
use App\Models\Employee;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/employees/{nik}', function($nik) {
    $employee = Employee::where('nik', $nik)->first();

    if (!$employee) {
        return response()->json([
            'success' => false,
            'error' => 'Employee not found'
        ], 404);
    }

    return response()->json([
        'success' => true,
        'nik' => $employee->nik,
        'name' => $employee->name,
        'is_active' => $employee->is_active,
    ]);
});

<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\IncomesController;
use App\Http\Controllers\ExpanseController;
use App\Http\Controllers\DailyReportController;
use App\Http\Controllers\MonthlyReportController;
use App\Http\Controllers\DetailReportController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthController::class, 'Register']);
Route::post('/login', [AuthController::class, 'Login'])->name('login');

Route::group(['middleware' => ['auth:sanctum']], function() {
    Route::get('/auth/check', [AuthController::class, 'Check']);
    Route::get('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [UserController::class, 'show']);
    Route::put('/profile', [UserController::class, 'update']);
    Route::resource('incomes', IncomesController::class)->only([
        'index', 'store', 'destroy', 'show', 'update'
    ]);
    Route::resource('expanse', ExpanseController::class)->only([
        'index', 'store', 'destroy', 'show', 'update'
    ]);
    Route::get('/daily-report/totals-incomes-expanse', [DailyReportController::class, 'getTotalIncomeExpanse']);
    Route::get('/daily-report/transaction-by-day', [DailyReportController::class, 'getTransactionsByDate']);
    Route::get('/monthly-report/monthly-report', [MonthlyReportController::class, 'getMonthlyReports']);
    Route::get('/monthly-report/detail-monthly-report', [DetailReportController::class, 'getMonthlyReportDetail']);
});

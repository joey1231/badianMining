<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SharingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */
Auth::routes();
Route::group(['middleware' => ['auth']], function () {
    Route::get('/', function () {
        return view('dashboad');
    });

    Route::get('/home', function () {
        return redirect('/');
    });
    Route::group(['prefix' => 'dashboard'], function () {
        Route::get('info', [DashboardController::class, 'info']);
        Route::get('info-share', [DashboardController::class, 'infoShare']);

    });
    Route::group(['prefix' => 'sales'], function () {
        Route::get('/all', [SalesController::class, 'all']);

        Route::get('', [SalesController::class, 'index']);

        Route::get('/search', [SalesController::class, 'search']);
        Route::get('/status/{status}', [SalesController::class, 'getSalesbyStatus']);
        Route::get('/create', [SalesController::class, 'create']);
        Route::post('', [SalesController::class, 'store']);
        Route::get('/info/{hash_id}', [SalesController::class, 'info']);
        Route::get('/send-to-queue/{hash_id}', [SalesController::class, 'resend']);
        Route::get('/download/{hash_id}', [SalesController::class, 'downloadInvoice']);
        Route::get('/show/{hash_id}', [SalesController::class, 'show']);
        Route::get('/edit/{hash_id}', [SalesController::class, 'edit']);
        Route::put('/{hash_id}', [SalesController::class, 'update']);
        Route::delete('/{hash_id}', [SalesController::class, 'destroy']);
    });

    Route::group(['prefix' => 'shares'], function () {
        Route::get('/all', [SharingController::class, 'all']);

        Route::get('', [SharingController::class, 'index']);

        Route::get('/search', [SharingController::class, 'search']);

        Route::get('/create', [SharingController::class, 'create']);
        Route::post('', [SharingController::class, 'store']);
        Route::get('/info/{hash_id}', [SharingController::class, 'info']);
        Route::get('/share-paid/{hash_id}', [SharingController::class, 'changeStatus']);
        Route::get('/shared-paid/{hash_id}', [SharingController::class, 'changeStatusShared']);
        Route::get('/send-to-queue/{hash_id}', [SharingController::class, 'resend']);
        Route::get('/download/{hash_id}', [SharingController::class, 'downloadInvoice']);
        Route::get('/show/{hash_id}', [SharingController::class, 'show']);
        Route::get('/edit/{hash_id}', [SharingController::class, 'edit']);
        Route::put('/{hash_id}', [SharingController::class, 'update']);
        Route::delete('/{hash_id}', [SharingController::class, 'destroy']);
    });
});

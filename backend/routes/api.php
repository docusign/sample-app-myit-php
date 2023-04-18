<?php

use App\Http\Controllers\Api\BulkSendEnvelopeController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\MonitorEventsController;
use App\Http\Controllers\Api\PermissionProfileController;
use App\Http\Controllers\Api\TokenController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\LogoutController;
use App\Http\Controllers\Api\UserExportController;
use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;

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

Route::post('login', LoginController::class);

Route::any('callback/event', EventController::class)
    ->name('callback-event');

Route::group([
    'middleware' => 'auth:api',
], function () {
    Route::get('users', UserController::class);

    Route::get('users/download', UserExportController::class);

    Route::get('token', TokenController::class);

    Route::get('equipments-and-software', DocumentController::class);

    Route::post('bulk-envelope-sending', BulkSendEnvelopeController::class);

    Route::get('permission-profile', [PermissionProfileController::class, 'index']);

    Route::post('permission-profile', [PermissionProfileController::class, 'create']);

    Route::delete('permission-profile', [PermissionProfileController::class, 'delete']);

    Route::post('logout', LogoutController::class);

    Route::get('monitor-alerts', MonitorEventsController::class);
});

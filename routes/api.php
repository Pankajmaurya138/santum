<?php

use Illuminate\Http\Request;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

use App\Http\Controllers\API\RegisterController;



// Route//

Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [RegisterController::class, 'login']);
Route::get('create_account', [RegisterController::class, 'createAccount'])->name('user.create_account');
Route::post('verifyOtp', [RegisterController::class, 'verifyOtp'])->name('user.verifyOtp');
Route::middleware('auth:sanctum')->group( function () {

    Route::post('send_sinup_email', [RegisterController::class, 'sendInvitionEmail'])->name('user.signup');
    Route::post('profileUpdate', [RegisterController::class, 'profileUpdate']);
});

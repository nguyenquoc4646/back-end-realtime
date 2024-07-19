<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\GroupController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Client\ChatGroupController;
use App\Http\Controllers\Client\ChatPrivateController;
use App\Http\Controllers\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\DependencyInjection\RegisterControllerArgumentLocatorsPass;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::apiResource('users', UserController::class);
Route::apiResource('groups',GroupController::class);
Route::get('/groups/{id}/edit', [GroupController::class,'edit']);
Route::get('/users/{id}/edit', [UserController::class, 'edit']);
Route::post('register', [AuthController::class,'register']);
Route::post('login', [AuthController::class,'login']);
Route::post('/users/forgot-password',[UserController::class,'forgotPassword']);
Route::post('/users/update-password',[UserController::class,'updatePassword']);
Route::get('/activate/{id}', [UserController::class,'activateMail']);
Route::group(['middleware' => 'api'], function () {
    Route::post('logout', [AuthController::class,'logout']);
    // Route::post('refresh',[AuthController::class,'refresh']);
    Route::post('me',[AuthController::class,'me']);
    Route::post('/upload-file-account',[UserController::class,'uploadFile']);
    Route::put('/update-infor/{id}',[AuthController::class,'updateInfor']);
   
});
Route::get('/chat-private/{idUserReciever}', [ChatPrivateController::class,'chatPrivate']);
Route::post('/send-message-private', [ChatPrivateController::class,'messagePrivate']);
Route::get('/chat-group/{idGroup}', [ChatGroupController::class,'chatGroup']);
Route::post('/send-message-group', [ChatGroupController::class,'messageGroup']);


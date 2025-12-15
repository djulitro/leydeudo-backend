<?php

use App\Http\Controllers\Api\Auth\PasswordResetController;
use App\Http\Controllers\Api\Users\AuthController;
use App\Http\Controllers\Api\Users\UserConfigController;
use App\Http\Controllers\Api\Users\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return response()->json(['message' => 'pong']);
});

// Rutas públicas de autenticación
Route::post('/login', [AuthController::class, 'login']);

// Rutas públicas de reset de contraseña
Route::post('/password/validate-token', [PasswordResetController::class, 'validateToken']);
Route::post('/password/reset', [PasswordResetController::class, 'resetPassword']);

// Rutas protegidas
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::get('/user/config', [UserConfigController::class, 'index']);
    Route::get('/user/check-permission/{permission}', [UserConfigController::class, 'checkPermission']);

    // Mantenedor de usuarios.
    Route::post('/users', [UserController::class, 'createUser'])->middleware('permission:users.create');
    Route::put('/users/{id_user}', [UserController::class, 'updateUser'])->middleware('permission:users.edit');
    Route::put('/users/{id_user}/disable', [UserController::class, 'disable'])->middleware('permission:users.edit');
    Route::put('/users/{id_user}/activate', [UserController::class, 'activate'])->middleware('permission:users.edit');
    Route::get('/users', [UserController::class, 'listUsers'])->middleware('permission:users.view');

    // Ruta para obtener los roles
    Route::get('/roles', [UserController::class, 'getRoles'])->middleware('permission:users.create');
});
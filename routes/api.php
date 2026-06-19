<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrganizationController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    Route::get('/organizations', [OrganizationController::class, 'index']);
    Route::post('/organizations', [OrganizationController::class, 'store']);

    Route::middleware(['owner'])->group(function () {
        Route::get('/organizations/{organization}', [OrganizationController::class, 'show']);
        Route::delete('/organizations/{organization}', [OrganizationController::class, 'destroy']);
        Route::get('/organizations/{organization}/reviews', [OrganizationController::class, 'getReviews']);
        Route::post('/organizations/{organization}/refresh', [OrganizationController::class, 'refresh']);
    });
});
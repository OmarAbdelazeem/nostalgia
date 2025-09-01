<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;

// Health check route for deployment platforms
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy', 
        'timestamp' => now(),
        'version' => '1.0.0'
    ]);
});

// Authentication routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('api.auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [UserController::class, 'profile']);

    // Categories routes
    Route::apiResource('categories', CategoryController::class);
    Route::post('/categories/{id}/update', [CategoryController::class, 'updateWithFormData']);

    // Products routes
    Route::apiResource('products', ProductController::class);
    
    // Separate image upload endpoint
    Route::post('/products/{product}/upload-image', [ProductController::class, 'uploadImage']);

    // Product image management routes
    Route::apiResource('products.images', ProductImageController::class);

    // User management routes
    Route::apiResource('users', UserController::class);

    // Roles and permissions routes
    Route::get('/roles', [RoleController::class, 'index']);
    Route::get('/permissions', [PermissionController::class, 'index']);
}); 
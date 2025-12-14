<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\RentalController;
use App\Models\User;
use App\Models\Rental;

/*
|--------------------------------------------------------------------------
| Health & Test Routes
|--------------------------------------------------------------------------
*/
Route::get('/health', fn () => response()->json(['status' => 'ok']));
Route::get('/test', fn () => response()->json(['message' => 'API is working']));

/*
|--------------------------------------------------------------------------
| Authentication - PUBLIC
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']); // ✅ critical
    Route::post('/login', [AuthController::class, 'login']);
});

/*
|--------------------------------------------------------------------------
| Authentication - PROTECTED
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->prefix('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});

/*
|--------------------------------------------------------------------------
| Public Rentals (READ ONLY)
|--------------------------------------------------------------------------
*/
Route::get('/rentals', [RentalController::class, 'index']);
Route::get('/rentals/{rental}', [RentalController::class, 'show']);

/*
|--------------------------------------------------------------------------
| Protected User Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'check.user.status'])->group(function () {

    /*
    |--------------------------------------------------
    | Authenticated User
    |--------------------------------------------------
    */
    Route::get('/user', [UserController::class, 'getCurrentUser']);

    /*
    |--------------------------------------------------
    | Profile
    |--------------------------------------------------
    */
    Route::put('/users/profile', [UserController::class, 'updateProfile']);

    /*
    |--------------------------------------------------
    | Rentals (CRUD)
    |--------------------------------------------------
    */
    Route::post('/rentals', [RentalController::class, 'store']);
    Route::put('/rentals/{rental}', [RentalController::class, 'update']);
    Route::delete('/rentals/{rental}', [RentalController::class, 'destroy']);
});

/*
|--------------------------------------------------------------------------
| Admin Routes (READ ONLY FOR NOW)
|--------------------------------------------------------------------------
| NOTE:
| These are intentionally LIMITED.
| Do NOT add logic-heavy closures here.
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->group(function () {

    Route::get('/dashboard/stats', function () {
        return response()->json([
            'users' => User::count(),
            'rentals' => Rental::count(),
            'activeRentals' => Rental::where('status', 'rented')->count()
        ]);
    });

    Route::get('/users', function () {
        return User::select('id', 'name', 'email', 'role', 'verified', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();
    });
});

/*
|--------------------------------------------------------------------------
| Legacy / Backward Compatibility
|--------------------------------------------------------------------------
| Keeps existing frontend calls alive
|--------------------------------------------------------------------------
*/
Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{user}', [UserController::class, 'show']);

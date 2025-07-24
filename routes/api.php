
<?php

use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthorsController;
use App\Http\Controllers\BookAuthorsController;
use App\Http\Controllers\BooksController;
use App\Http\Controllers\LoanItemController;
use App\Http\Controllers\LoansController;
use App\Http\Controllers\ReturnItemController;
use App\Http\Controllers\ReturnsController;
use App\Http\Controllers\UserController;
use App\Models\Loans;
use Illuminate\Support\Facades\DB;




Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    //Akun
    // Route::controller(UserController::class)->group(function(){
    //     Route::get('/user', 'index');
    //     Route::post('/user/store', 'store');
    //     Route::patch('/user/{id}/update', 'update');
    //     Route::get('/user/{id}','show');
    //     Route::delete('/user/{id}', 'destroy');
    // });

    Route::apiResource('user', UserController::class);
    Route::apiResource('author', AuthorsController::class);
    Route::apiResource('book', BooksController::class);
    Route::apiResource('loans', LoansController::class);
    Route::apiResource('bookDetails', BookAuthorsController::class);
    // ------------------------------------------------------------  //
    Route::get('loan-items', [LoanItemController::class, 'index']);
    Route::get('loan-items/{loan_id}', [LoanItemController::class, 'show']);
    Route::post('loan-items/{loan_id}', [LoanItemController::class, 'store']);
    Route::put('loan-items/{id}', [LoanItemController::class, 'update']);
    Route::delete('loan-items/{id}', [LoanItemController::class, 'destroy']);
    
    Route::get('returns', [ReturnsController::class, 'index']);
    Route::get('returns/{id}', [ReturnsController::class, 'show']);
    Route::get('returns/by-loan/{loan_id}', [ReturnsController::class, 'showByLoan']);
    Route::post('/returns', [ReturnsController::class, 'store']);
    Route::put('returns/{id}', [ReturnsController::class, 'update']);
    Route::delete('returns/{id}', [ReturnsController::class, 'destroy']);


        // Tambahkan route return-items
    Route::prefix('return-items')->group(function () {
        Route::get('/', [ReturnItemController::class, 'index']);
        Route::get('/{id}', [ReturnItemController::class, 'show']);
        Route::post('/{load_id}', [ReturnItemController::class, 'store']);
        Route::put('/{id}', [ReturnItemController::class, 'update']);
        Route::delete('/{id}', [ReturnItemController::class, 'destroy']);
    });
    Route::get('/return-items/by-return/{return_id}', [ReturnItemController::class, 'showByReturnId']);
    Route::post('/return-items/{return_id}/items', [ReturnItemController::class, 'saveReturnItems']);
        Route::get('/dashboard-counts', function () {
        return response()->json([
            'users' => \App\Models\User::count(),
            'books' => \App\Models\Books::count(),
            'loan' => \App\Models\Loans::count(),
            'return' => \App\Models\Returns::count(),
        ]);
    });
Route::post('/register', [UserController::class, 'register']);



});





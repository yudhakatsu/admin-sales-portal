<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\RecomendationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\CustomersController;

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/products/list-filter', [RecomendationController::class, 'filter'])->name('products.filter');

    // Middleware untuk kategori & produk (admin & gudang)
    Route::middleware(RoleMiddleware::class . ':admin,gudang')->group(function () {
        Route::resource('categories', CategoryController::class);
        Route::resource('products', ProductController::class);
    });

    // Middleware hanya untuk admin (user management)
    Route::middleware(RoleMiddleware::class . ':admin')->group(function () {
        Route::resource('users', UserController::class);
    });

    // Route untuk transaksi
    Route::get('/transactions', function () {
        return redirect()->route('transactions.create'); // Akses langsung diarahkan ke create
    });


    // Route untuk transaksi
    Route::resource('transactions', TransactionController::class);
    Route::patch('/transactions/{transaction}/update-payment-status', [TransactionController::class, 'updatePaymentStatus'])->name('transactions.updatePaymentStatus');
    Route::resource('recomendation', RecomendationController::class);
    Route::resource('customers', CustomersController::class);
    Route::resource('cart', CartController::class);
    Route::put('cart/{cart}', [CartController::class, 'update'])->name('cart.update');
    Route::get('/cart/{cart}/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
});

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/get-products-by-budget', [TransactionController::class, 'getProductsByBudget']);


Route::get('/transactions/customers/search', [TransactionController::class, 'searchCustomers']);

Route::get('/transactions/{id}/print', [TransactionController::class, 'print'])->name('transactions.print');

Route::post('/transactions/{id}/download-wa', [TransactionController::class, 'downloadPdfAndOpenWa'])->name('transactions.downloadPdfAndOpenWa');
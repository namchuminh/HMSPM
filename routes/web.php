<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;


Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');


// Middleware bảo vệ các route cần đăng nhập
Route::middleware(['auth.middleware'])->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Quản lý sản phẩm
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index'); // Danh sách sản phẩm
        Route::get('/create', [ProductController::class, 'create'])->name('create'); // Form thêm sản phẩm
        Route::post('/store', [ProductController::class, 'store'])->name('store'); // Lưu sản phẩm mới
        Route::get('/{id}/edit', [ProductController::class, 'edit'])->name('edit'); // Chỉnh sửa sản phẩm
        Route::put('/{id}/update', [ProductController::class, 'update'])->name('update'); // Cập nhật sản phẩm
        Route::delete('/{id}/delete', [ProductController::class, 'destroy'])->name('destroy'); // Xóa sản phẩm
    });

    // Quản lý nhập kho
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::get('/create', [InventoryController::class, 'create'])->name('create');
        Route::post('/store', [InventoryController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [InventoryController::class, 'edit'])->name('edit');
        Route::put('/{id}/update', [InventoryController::class, 'update'])->name('update'); // Cập nhật sản phẩm
        Route::delete('/{id}/delete', [InventoryController::class, 'destroy'])->name('destroy'); // Xóa sản phẩm
    });

    // Quản lý mượn – thu hồi sản phẩm
    Route::prefix('loans')->name('loans.')->group(function () {
        Route::get('/', [LoanController::class, 'index'])->name('index');
        Route::post('/borrow', [LoanController::class, 'borrow'])->name('borrow');
        Route::post('/return/{id}', [LoanController::class, 'return'])->name('return');
    });

    // Lịch sử giao dịch
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');

    // Báo cáo
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/generate', [ReportController::class, 'generate'])->name('generate');
    });

    // Quản lý ngươi dùng
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index'); 
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/store', [UserController::class, 'store'])->name('store'); 
        Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit'); 
        Route::put('/{id}/update', [UserController::class, 'update'])->name('update'); 
        Route::delete('/{id}/delete', [UserController::class, 'destroy'])->name('destroy'); 
    });

    // Quản lý thông báo
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');

    Route::get('/profile', [UserController::class, 'profile'])->name('users.profile');
    Route::post('/profile', [UserController::class, 'profileUpdate'])->name('users.profileUpdate');

    Route::prefix('loans')->name('loans.')->group(function () {
        Route::get('/', [LoanController::class, 'index'])->name('index');
        Route::get('/create', [LoanController::class, 'create'])->name('create');
        Route::post('/store', [LoanController::class, 'store'])->name('store');
        Route::put('/{id}/return', [LoanController::class, 'update'])->name('update');
        Route::delete('/{id}/delete', [LoanController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-action', [LoanController::class, 'bulkAction'])->name('bulkAction');
    });
    

    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/', [TransactionController::class, 'index'])->name('index');
        Route::get('/create', [TransactionController::class, 'create'])->name('create');
        Route::post('/store', [TransactionController::class, 'store'])->name('store');
        Route::get('/{id}', [TransactionController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [TransactionController::class, 'edit'])->name('edit');
        Route::put('/{id}/update', [TransactionController::class, 'update'])->name('update');
        Route::delete('/{id}/delete', [TransactionController::class, 'destroy'])->name('destroy');
    });
});

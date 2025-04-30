<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AuthAdmin;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/', [HomeController::class, 'index'])->name('home.index');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function()
{
    Route::get('/account-dashboard', [UserController::class, 'index'])->name('user.index');
});

Route::middleware(['auth', AuthAdmin::class])->group(function()
{
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/admin/publishers', [AdminController::class, 'publishers'])->name('admin.publishers');
    Route::get('/admin/publisher/add', [AdminController::class, 'add_publisher'])->name('admin.publisher.add');
    Route::post('/admin/publisher/store', [AdminController::class, 'publisher_store'])->name('admin.publisher.store');
    Route::get('/admin/publisher/edit/{id}', [AdminController::class, 'publisher_edit'])->name('admin.publisher.edit');
    Route::put('/admin/publisher/update', [AdminController::class, 'publisher_update'])->name('admin.publisher.update');
    Route::delete('/admin/publisher/{id}/delete', [AdminController::class, 'publisher_delete'])->name('admin.publisher.delete');
});

// Route::get('/{product_slug}', [ShopController::class, 'product_details'])->name('product.details');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');

Route::get('/product_1', function()
{
    return view('layouts.product');
});

// Route::get('/cart', function()
// {
//     return view('layouts.cart');
// });

require __DIR__.'/auth.php';

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

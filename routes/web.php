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

    Route::get('/admin/categories', [AdminController::class, 'categories'])->name('admin.categories');
    Route::get('/admin/category/add', [AdminController::class, 'category_add'])->name('admin.category.add');
    Route::post('/admin/category/store', [AdminController::class, 'category_store'])->name('admin.category.store');
    Route::get('/admin/category/{id}/edit', [AdminController::class, 'category_edit'])->name('admin.category.edit');
    Route::put('/admin/category/update', [AdminController::class, 'category_update'])->name('admin.category.update');
    Route::delete('/admin/category/{id}/delete', [AdminController::class, 'category_delete'])->name('admin.category.delete');

    Route::get('/admin/products', [AdminController::class, 'products'])->name('admin.products');
    Route::get('/admin/product/add', [AdminController::class, 'product_add'])->name('admin.product.add');
    Route::post('/admin/product/store', [AdminController::class, 'product_store'])->name('admin.product.store');
    Route::get('/admin/product/{id}/edit', [AdminController::class, 'product_edit'])->name('admin.product.edit');
    Route::put('/admin/product/update', [AdminController::class, 'product_update'])->name('admin.product.update');
    Route::delete('/admin/product/{id}/delete', [AdminController::class, 'product_delete'])->name('admin.product.delete');

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

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/', [HomeController::class, 'index'])->name('home.index');
Route::get('/aboutus', [HomeController::class, 'aboutus'])->name('home.aboutus');
Route::get('/contact', [HomeController::class, 'contact'])->name('home.contact');
Route::get('/shop', [HomeController::class, 'shop'])->name('home.shop');

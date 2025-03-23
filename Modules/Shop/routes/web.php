<?php

use Illuminate\Support\Facades\Route;
use Modules\Shop\Http\Controllers\ShopController;
use Modules\Shop\Http\Controllers\ProductController;

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/category/{categorySlug}', [ProductController::class, 'category'])->name('products.category');
Route::get('/tag/{tagSlug}', [ProductController::class, 'tag'])->name('products.tag');
Route::get('/{categorySlug}/{productSlug}', [ProductController::class, 'show'])->name('products.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('shop', ShopController::class)->names('shop');
});
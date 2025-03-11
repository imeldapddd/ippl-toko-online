<?php

use Illuminate\Support\Facades\Route;
use Modules\Shop\Http\Controllers\ShopController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('shop', ShopController::class)->names('shop');
});

<?php


use Illuminate\Support\Facades\Route;

Route::middleware(['auth:craft', 'craft.web'])->group(function() {
    Route::get(
        'element-seeder',
        [\Anubarak\Seeder\Http\Controllers\SeederController::class, 'index']
    )->name('element-seeder-index');

    Route::post(
        'element-seeder-clean',
        [\Anubarak\Seeder\Http\Controllers\SeederController::class, 'clean']
    )->name('element-seeder-clean');

});


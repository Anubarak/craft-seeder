<?php


use Illuminate\Support\Facades\Route;

Route::middleware(['craft.web'])->group(function() {
    Route::get(
        'element-seeder',
        [\Anubarak\Seeder\Http\Controllers\SeederController::class, 'index']
    )->name('element-seeder-index');

    Route::post(
        'element-seeder-clean',
        [\Anubarak\Seeder\Http\Controllers\SeederController::class, 'clean']
    )->name('element-seeder-clean');


    Route::get(
        'actions/element-seeder/numerize-content-modal',
        [\Anubarak\Seeder\Http\Controllers\SeederController::class, 'numerizeContentModal']
    );
    Route::post(
        'actions/element-seeder/numerize-elements',
        [\Anubarak\Seeder\Http\Controllers\SeederController::class, 'numerizeElements']
    );

    Route::post(
        'actions/element-seeder/generate-content',
        [\Anubarak\Seeder\Http\Controllers\SeederController::class, 'generateContent']
    );

    Route::get(
        'actions/element-seeder/element-content-modal',
        [\Anubarak\Seeder\Http\Controllers\SeederController::class, 'elementContentModal']
    );
});


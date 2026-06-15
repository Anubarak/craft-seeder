<?php


use Illuminate\Support\Facades\Route;

Route::middleware(['auth:craft', 'craft.web'])->group(function() {
    Route::get(
        'element-seeder/element-content-modal',
        [\Anubarak\Seeder\Http\Controllers\SeederController::class, 'elementContentModal']
    );
    Route::get(
        'element-seeder/element-matrix-modal',
        [\Anubarak\Seeder\Http\Controllers\SeederController::class, 'elementMatrixModal']
    );
    Route::post(
        'element-seeder/generate-content',
        [\Anubarak\Seeder\Http\Controllers\SeederController::class, 'generateContent']
    );

    Route::get(
        'element-seeder/numerize-content-modal',
        [\Anubarak\Seeder\Http\Controllers\SeederController::class, 'numerizeContentModal']
    );
    Route::post(
        'element-seeder/numerize-elements',
        [\Anubarak\Seeder\Http\Controllers\SeederController::class, 'numerizeElements']
    );
});


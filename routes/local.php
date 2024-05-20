<?php

use App\Support\PostFixture;
use Illuminate\Support\Facades\Route;

Route::middleware('api')->group(function () {
    Route::get('post-content', function() {
        return PostFixture::getFixtures()->random();
    })->name('post_content');
});

Route::middleware('web')->group(function () {

});

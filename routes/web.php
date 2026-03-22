<?php

use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::get('/about', static function () {
    ob_start();
    phpinfo();

    return ob_get_clean();
});

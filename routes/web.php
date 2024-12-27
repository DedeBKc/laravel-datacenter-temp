<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/employees', function () {
    $data = \DB::table('employees')->paginate(10);
    return $data;
});

Route::get('/salaries', function () {
    $data = \DB::table('salaries')->paginate(10);
    return $data;
});

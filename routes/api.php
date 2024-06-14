<?php

use App\Http\Controllers\BombingController;
use App\Http\Controllers\AdminPanelController;
use App\Http\Controllers\BlacklistController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Classes\ApplicationStatus;



Route::get('/cron', function(){
    Artisan::call("schedule:run");
    $output = Artisan::output();
});

Route::get('/clean-logs', function(){
    Artisan::call("logs:clean");
    $output = Artisan::output();
    return $output;
});
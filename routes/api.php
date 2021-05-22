<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;





Route::apiResource('posts', \App\Http\Controllers\Api\PostController::class)
        ->middleware('auth:api');

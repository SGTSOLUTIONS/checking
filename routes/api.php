<?php

use App\Http\Controllers\TallyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/tally/receive', [TallyController::class, 'receiveXML']);

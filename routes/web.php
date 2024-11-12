<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function() { return view('test'); });

Route::get('/csrfReset', [App\Http\Controllers\ExtraController::class, 'csrfReset'])->name('csrfReset');

Route::group(['prefix' => 'auth'], function() {	
    Route::get('register', [App\Http\Controllers\Auth\AuthController::class, 'register'])->name('register');
	Route::post('findUser', [App\Http\Controllers\Auth\AuthController::class, 'findUser'])->name('findUser');
	Route::post('signUp', [App\Http\Controllers\Auth\AuthController::class, 'signUp'])->name('signUp');
	Route::get('login', [App\Http\Controllers\Auth\AuthController::class, 'login'])->name('login');
	Route::get('identify', [App\Http\Controllers\Auth\AuthController::class, 'identify'])->name('identify');
	Route::post('checkToken', [App\Http\Controllers\Auth\AuthController::class, 'checkToken'])->name('checkToken');
	Route::get('logout', [App\Http\Controllers\Auth\AuthController::class, 'logout'])->name('logout');
	Route::post('signIn', [App\Http\Controllers\Auth\AuthController::class, 'signIn'])->name('signIn');
	Route::get('findId', [App\Http\Controllers\Auth\AuthController::class, 'findId'])->name('findId');
	Route::get('findPassword', [App\Http\Controllers\Auth\AuthController::class, 'findPassword'])->name('findPassword');
	Route::post('findAuth', [App\Http\Controllers\Auth\AuthController::class, 'findAuth'])->name('findAuth');
});

Route::group(['prefix' => 'jstester'], function() {
	Route::get('view', [App\Http\Controllers\JsTesterController::class, 'view'])->name('jsView');
	Route::post('save/{code_key?}', [App\Http\Controllers\JsTesterController::class, 'saveTestCode'])->name('saveTestCode');
	Route::post('run', [App\Http\Controllers\JsTesterController::class, 'runCode'])->name('runCode');
	Route::post('load', [App\Http\Controllers\JsTesterController::class, 'loadCode'])->name('loadCode');
});
/*
Route::group(['middleware' => ['auth:user']], function() {
	
});
*/
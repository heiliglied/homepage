<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'test'], function() {
	Route::get('/test', function() { return view('test'); });
	Route::get('/asyncTest', function() { return view('asyncTest'); });
	Route::get('/getAsyncData', [App\Http\Controllers\ExtraController::class, 'getAsyncData'])->name('getAsyncData');
	Route::post('/likepubsub', [App\Http\Controllers\ExtraController::class, 'likepubsub'])->name('likepubsub');
});

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
	Route::post('emailCheck', [App\Http\Controllers\Auth\AuthController::class, 'emailCheck'])->name('emailCheck');
	Route::post('idCheck', [App\Http\Controllers\Auth\AuthController::class, 'idCheck'])->name('idCheck');
});

Route::group(['prefix' => 'jstester'], function() {
	Route::get('view', [App\Http\Controllers\JsTesterController::class, 'view'])->name('jsView');
	Route::post('save/{code_key?}', [App\Http\Controllers\JsTesterController::class, 'saveTestCode'])->name('saveTestCode');
	Route::post('run', [App\Http\Controllers\JsTesterController::class, 'runCode'])->name('runCode');
	Route::post('load', [App\Http\Controllers\JsTesterController::class, 'loadCode'])->name('loadCode');
	Route::post('viewCode', [App\Http\Controllers\JsTesterController::class, 'viewCode'])->name('viewCode');
	Route::post('deleteCode', [App\Http\Controllers\JsTesterController::class, 'deleteCode'])->name('deleteCode');
});

Route::group(['middleware' => ['auth']], function() {
	Route::group(['prefix' => 'my'], function() {
		Route::get('/', [App\Http\Controllers\MyPageController::class, 'my'])->name('myView');
		Route::post('profile', [App\Http\Controllers\MyPageController::class, 'profile'])->name('myProfile');
		Route::post('update', [App\Http\Controllers\MyPageController::class, 'update'])->name('updateProfile');
	});
	
	Route::group(['prefix' => 'board'], function() {
		Route::get('/', [App\Http\Controllers\BoardController::class, 'board'])->name('board');
		Route::get('write', [App\Http\Controllers\BoardController::class, 'write'])->name('boardWrite');
		Route::post('imageUpload', [App\Http\Controllers\BoardController::class, 'imageUpload'])->name('imageUpload');
		Route::post('regist', [App\Http\Controllers\BoardController::class, 'regist'])->name('boardRegist');
		Route::get('boardList', [App\Http\Controllers\BoardController::class, 'boardList'])->name('boardList');
		Route::get('modify/{id}', [App\Http\Controllers\BoardController::class, 'modify'])->name('boardModify');
		Route::get('getBoard', [App\Http\Controllers\BoardController::class, 'getBoard'])->name('getBoard');
		Route::get('getView', [App\Http\Controllers\BoardController::class, 'getView'])->name('getView');
		Route::get('view/{id}', [App\Http\Controllers\BoardController::class, 'boardView'])->name('boardView');
		Route::delete('deleteFile/{id}', [App\Http\Controllers\BoardController::class, 'deleteFile'])->name('deleteFile');
		Route::get('downloadFile/{id}', [App\Http\Controllers\BoardController::class, 'downloadFile'])->name('downloadFile');
		Route::delete('deleteBoard/{id}', [App\Http\Controllers\BoardController::class, 'deleteBoard'])->name('deleteBoard');
		Route::patch('censorBoard/{id}', [App\Http\Controllers\BoardController::class, 'censorBoard'])->name('censorBoard');
		Route::get('getReply', [App\Http\Controllers\BoardController::class, 'getReply'])->name('getReply');
		Route::post('setReply', [App\Http\Controllers\BoardController::class, 'setReply'])->name('setReply');
		Route::patch('replyCensor/{id}', [App\Http\Controllers\BoardController::class, 'replyCensor'])->name('replyCensor');
		Route::delete('deleteReply/{id}', [App\Http\Controllers\BoardController::class, 'deleteReply'])->name('deleteReply');
	});
});
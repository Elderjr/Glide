<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('home');
});

//home
Route::post('/autenticar','AuthenticationController@authenticate');
Route::post('/registrar','UserController@store');

//user
Route::get('/usuario', function(){
    return view('general');
});


//grupos
Route::get('/usuario/grupos/cadastrar', function(){
    return view('cadastrarGrupo');
});
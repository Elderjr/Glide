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


Route::get('/usuario/logout', "GeneralController@logout");

//home
Route::post('/autenticar','AuthenticationController@authenticate');
Route::post('/registrar','UserController@store');

//user
Route::get('/usuario', 'GeneralController@index');

//grupos
Route::get('/usuario/grupos', 'GroupController@index');
Route::get('/usuario/grupos/cadastrar', 'GroupController@create');
Route::post('/usuario/grupos/cadastrar', 'GroupController@store');
Route::post('/usuario/grupos/{groupId}/setAdminAsTrue', 'GroupController@setAdminAsTrue');
Route::post('/usuario/grupos/{groupId}/setAdminAsFalse', 'GroupController@setAdminAsFalse');
Route::post('/usuario/grupos/{groupId}/sair', 'GroupController@leaveGroup');
Route::post('/usuario/grupos/{groupId}/removeMember', 'GroupController@removeMember');
Route::post('/usuario/grupos/{groupId}/storeMember', 'GroupController@storeMember');
Route::get('/usuario/grupos/{groupId}', 'GroupController@show');
Route::post('/usuario/grupos/{groupId}/edit', 'GroupController@edit');

//despesa
Route::get('/usuario/despesas/cadastrar', 'BillController@create');
Route::post('/usuario/despesas/cadastrar', 'BillController@store');
Route::get('/usuario/despesas/pendentes', 'BillController@pendingBills');
Route::get('/usuario/despesas/{id}', 'BillController@show');


//pagamentos
Route::get('/usuario/pagamentos', 'PaymentController@index');
Route::get('/usuario/pagamentos/cadastrar', 'PaymentController@create');
Route::post('/usuario/pagamentos/cadastrar', 'PaymentController@store');
Route::get('/usuario/pagamentos/rollback/{id}', 'PaymentController@rollback');
Route::get('/usuario/pagamentos/{id}', 'PaymentController@show');


//requerimentos
Route::get('/usuario/requerimentos', 'RequerimentController@index');
Route::get('/usuario/requerimentos/cadastrar', 'RequerimentController@create');
Route::post('/usuario/requerimentos/cadastrar', 'RequerimentController@store');
Route::get('/usuario/requerimentos/{id}', 'RequerimentController@show');
Route::get('/usuario/requerimentos/{id}/aceitar', 'RequerimentController@showAccept');
Route::post('/usuario/requerimentos/{id}/aceitar', 'RequerimentController@accept');


//historico
Route::get('/usuario/historico', "HistoryController@index");

//api
Route::get('/api/usuario/{username}','ApiController@getUserByUsername');
Route::get('/api/grupo/{groupId}', 'ApiController@getGroupById');

//test
Route::get('/test', function(){
    return view('test');
});
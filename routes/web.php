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
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::group(['middleware' => 'auth'], function () {
    Route::get('/patient-list', 'UserController@patientList')->name('patientList');
    Route::get('/patient-profile/{id}', 'UserController@patientProfile')->name('patientProfile');
    Route::get('/doctor-list', 'UserController@doctorList')->name('doctorList');
    Route::get('/ticket-list', 'UserController@ticketList')->name('ticketList');
    Route::get('/ticket-create', 'UserController@createTicket')->name('createTicket');
    Route::post('/ticket-save', 'UserController@saveTicket')->name('saveTicket');
    Route::get('/chats-list', 'UserController@chatsList')->name('chatsList');
});
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

Route::post('/class/{id}/answerit','MainController@answerit');
Route::post('/class/{id}/subanswer','MainController@subanswer');
Route::post('/class/{id}/deleteanswer','MainController@deleteanswer');
Route::post('/class/{id}/markassolved','MainController@markassolved');
Route::post('/class/{id}/notsolved','MainController@notsolved');
Route::post('/class/{id}/remove','MainController@deletequestion');
Route::post('/class/{id}/vote','MainController@vote');
Route::get('/class/{id}','MainController@qanda');
Route::post('/class/{id}/post','MainController@post');
Route::post('/class/{id}/addclass','MainController@addclass');

Auth::routes();
Route::get('/logout', function () {
  Auth::logout();
  return redirect('/');
});

Route::get('/home', 'HomeController@index');
Route::get('/newclass', function () {
    return view('newclass');
});
Route::post('/newclass', 'MainController@newclass');
<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::get('articles', 'ArticleController@index')->name('articles');
Route::get('articles/{id}/comments', 'ArticleController@comments')->name('comments');
Route::get('tags', 'ArticleController@tags')->name('tags');
Route::get('tags/{id}/articles', 'ArticleController@tagsArticles')->name('tagsArticles');

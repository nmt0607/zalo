<?php

use Illuminate\Support\Facades\Route;

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

Route::post('/signup', 'App\Http\Controllers\AuthController@signup');
Route::post('/login', 'App\Http\Controllers\AuthController@login');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', 'App\Http\Controllers\AuthController@logout');
    Route::post('/add_post', 'App\Http\Controllers\PostController@store');
    Route::post('/get_post', 'App\Http\Controllers\PostController@show');
    Route::post('/edit_post', 'App\Http\Controllers\PostController@update');
    Route::post('/delete_post', 'App\Http\Controllers\PostController@destroy');
    Route::post('/report_post', 'App\Http\Controllers\PostController@report');
    Route::post('/get_list_posts', 'App\Http\Controllers\PostController@get_list_posts');
    Route::post('/check_new_item', 'App\Http\Controllers\PostController@check_new_item');
    Route::post('/like', 'App\Http\Controllers\PostController@like');

    Route::post('/get_comment', 'App\Http\Controllers\CommentController@getComment');
    Route::post('/set_comment', 'App\Http\Controllers\CommentController@setComment');
    Route::post('/edit_comment', 'App\Http\Controllers\CommentController@update');
    Route::post('/del_comment', 'App\Http\Controllers\CommentController@destroy');

    Route::post('/get_list_conversation', 'App\Http\Controllers\ConversationController@index');
    Route::post('/get_conversation', 'App\Http\Controllers\ConversationController@getConversation');
    Route::post('/delete_conversation', 'App\Http\Controllers\ConversationController@delete_conversation');
    
    Route::post('/delete_message', 'App\Http\Controllers\MessageController@delete_message');

    Route::post('/get_user_info', 'App\Http\Controllers\UserController@get_user_info');
    Route::post('/set_user_info', 'App\Http\Controllers\UserController@set_user_info');
    Route::post('/change_password', 'App\Http\Controllers\UserController@change_password');
    
    Route::post('/set_block_user', 'App\Http\Controllers\BlockController@set_block_user');
    Route::post('/set_block_diary', 'App\Http\Controllers\BlockController@set_block_diary');
});

Route::post('/get_admin_permission', 'App\Http\Controllers\AdminController@get_admin_permission');
Route::post('/get_user_list', 'App\Http\Controllers\AdminController@get_user_list');
Route::post('/set_role', 'App\Http\Controllers\AdminController@set_role');
Route::post('/set_user_state', 'App\Http\Controllers\AdminController@set_user_state');
Route::post('/delete_user', 'App\Http\Controllers\AdminController@delete_user');
Route::post('/get_analyst_result', 'App\Http\Controllers\AdminController@get_analyst_result');
Route::post('/get_user_basic_info', 'App\Http\Controllers\AdminController@get_user_basic_info');

Route::post('/search', 'App\Http\Controllers\SearchController@search');
Route::post('/get_saved_search', 'App\Http\Controllers\SearchController@get_saved_search');
Route::post('/del_saved_search', 'App\Http\Controllers\SearchController@del_saved_search');

Route::post('/get_user_friends', 'App\Http\Controllers\FriendController@get_user_friends');
Route::post('/set_request_friend', 'App\Http\Controllers\FriendController@set_request_friend');
Route::post('/get_requested_friend', 'App\Http\Controllers\FriendController@get_requested_friend');
Route::post('/set_accept_friend', 'App\Http\Controllers\FriendController@set_accept_friend');
Route::post('/get_suggested_list_friends', 'App\Http\Controllers\FriendController@get_suggested_list_friends');



Route::post('/get_verify_code', 'App\Http\Controllers\VerifyCodeController@get_verify_code');
Route::post('/check_verify_code', 'App\Http\Controllers\VerifyCodeController@check_verify_code');




// Route::post('/set_official_account', 'App\Http\Controllers\AuthController@set_official_account'); // Co neu ten api nhung khong co huong dan

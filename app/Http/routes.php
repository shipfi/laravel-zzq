<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
/**
 * test
 */


/**
 * 通讯录
 */

Route::get('departments/create','YcController@departmentsCreate');
Route::get('users/create',['as'=>'createUser','uses'=>'YcController@userCreate']);
Route::post('users/create',['as'=>'postCreateUser','uses'=>'YcController@postUserCreate']);
Route::get('departments','YcController@departmentsList');
Route::post('departments/create',['as'=>'postDepartmentCreate','uses'=>'YcController@postDepartmentCreate']);
Route::get('departments/{departmentid}/user/list',['as'=>'departmentlist','uses'=>'YcController@userList']);

/**
 * 菜单
 *
 */

Route::get('menus/create','YcController@menuCreate');
Route::get('menus/destroy','YcController@menuDestroy');


/**
 * 应用授权
 *
 */

Route::get('apps/authorization','YcController@authorization');

/**
 * 身份
 */
Route::get('identity',['middleware'=>'wx-auth','as'=>'identity','uses'=>'YcController@identity']);

Route::get('wx-auth', ['as' => 'wx-auth', 'uses' => 'WeixinAuth\WeixinAuthController@auth']);

/**
 * 事件
 */

Route::any('event',['as'=>'event','uses'=>'YcController@event']);

Route::any('/',['as'=>'index','uses'=>'YcController@index']);


Route::get('auth-success',['as'=>'auth-success','uses'=>'YcController@authCallBack']);
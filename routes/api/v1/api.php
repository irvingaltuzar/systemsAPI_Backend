<?php

use Illuminate\Http\Request;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::get('check', 'Auth\LoginController@check');
// Route::get('logout', 'Auth\LoginController@logout');
// // Route::post('login', 'Auth\LoginController@login');
// Route::group([
//     'prefix' => 'auth'
// ], function () {
//     Route::post('login', 'Xamarin\Auth\AuthController@login');
//     Route::post('signup', 'Auth\AuthController@signUp');

//     Route::group([
//       'middleware' => 'auth:api'
//     ], function() {
//         Route::post('logout', 'Xamarin\Auth\AuthController@logout');
//         Route::get('check', 'Xamarin\Auth\AuthController@user');
//         Route::get('getPermission','Xamarin\Permission\PermissionController@getPermission');
//         Route::get('getPersonal','Xamarin\InfoIntelisis\PersonalController@getPersonal');
//         Route::post('storePermission','Xamarin\Permission\PermissionController@store');
//         Route::post('cancelPermission','Xamarin\Permission\PermissionController@cancel');
//         Route::post('addFirm','Xamarin\Permission\FirmController@addFirm');
//         Route::get('permissionConcept','Xamarin\Permission\PermissionController@concept');
//         Route::get('anotherConceptPermission','Xamarin\Permission\PermissionController@anotherConcept');
//         Route::get('permissionCompleted','Xamarin\Permission\PermissionController@permissionCompleted');
//         Route::get('permissionCancel','Xamarin\Permission\PermissionController@permissionCancel');
//         Route::get('permissionRequested','Xamarin\Permission\PermissionController@permissionRequested');
//         Route::get('requestsToSing','Xamarin\Permission\PermissionController@requestsToSing');
//         Route::post('rejectRequest','Xamarin\Permission\PermissionController@rejectRequest');
//         Route::get('workerRequests','Xamarin\Permission\PermissionController@workerRequests');
//         Route::get('extension','Xamarin\ExtensionController@index');
//         //Route::get('temporal','Xamarin\ExtensionController@temporal');
//         Route::get('benefits','Xamarin\BenefitsController@index');
//     });
// });

Route::prefix('/user')->group(function() {
	Route::get('/check-token/{id}', 'ApiLoginController@checkToken');
	Route::post('/login', 'ApiLoginController@login');
});

Route::prefix('/test')->middleware('auth:api')->group(function() {
	Route::get('/any', 'ApiLoginController@test');
});


Route::group(['middleware' =>['verifyBearerToken']],function(){
	Route::prefix('/rrhh')->group(function() {
		Route::post('assist-control/last-check', 'Alfa\RecursosHumanos\ReporteAsistenciaController@lastCheck');
	});
});


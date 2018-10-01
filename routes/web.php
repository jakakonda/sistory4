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

//Route::get('/', function () {
//    return view('index');
//});


Route::get('/lang', "IndexController@lang")->name("index.lang#get");
Route::get('/lang/{lang}', "IndexController@lang")->name("index.lang#get");

Route::get('/', "IndexController@index")->name("index.index#get");
Route::get('/search', "SearchController@index")->name("search.index#get");
Route::get('/advanced-search', "SearchController@advanced")->name("search.advanced#get");
Route::get('/details/{hdl}', "DetailsController@index")->name("details.index#get");

Route::get('/ajax/{name}', "AjaxController@index")->name("ajax.index#get");

Route::get('/test', "TestController@index")->name("test.index#get");
Route::get('/test/{id}', "TestController@index")->name("test.index#get");

Route::get('/oai', "OaiController@index")->name("oai.index#get");


// *** Storage ***
Route::get('/storage/preview', "StorageController@preview")->name("storage.preview#get");
Route::get('/storage/mets', "StorageController@mets")->name("storage.mets#get");
Route::get('/xsd/{name}', "XsdController@index")->name("xsd.index#get");



// *** Admin ***
Route::group(['prefix' => 'admin'], function () {

    Route::get('login', "Admin\\LoginController@index")->name("admin.login.index#get");
    Route::post('login', "Admin\\LoginController@index")->name("admin.login.index#post");
    Route::get('logout', "Admin\\LogoutController@index")->name("admin.logout.index#get");

    Route::group(['middleware' => 'auth'], function(){
        Route::get('/', "Admin\\IndexController@index")->name("admin.index.index#get");

        // API routes
        Route::group(['prefix' => 'api'], function () {

            Route::post('initial-data', "Admin\\Api\\Initial@initialData")->name("admin.api.initial-data#post");

            Route::post('entity-list', "Admin\\Api\\Entities@entityList")->name("admin.api.entity-list#post");
            Route::post('entity-list-db', "Admin\\Api\\Entities@entityListDb")->name("admin.api.entity-list-db#post");
            Route::post('reserve-entity-id', "Admin\\Api\\Entities@reserveEntityId")->name("admin.api.reserve-entity-id#post");
            Route::post('save-entity', "Admin\\Api\\Entities@saveEntity")->name("admin.api.save-entity#post");
            Route::post('delete-entity', "Admin\\Api\\Entities@deleteEntity")->name("admin.api.delete-entity#post");
            Route::post('entity-hierarchy', "Admin\\Api\\Entities@entityHierarchy")->name("admin.api.entity-hierarchy#post");

            Route::post('file-list', "Admin\\Api\\Files@fileList")->name("admin.api.file-list#post");
            Route::post('save-file', "Admin\\Api\\Files@saveFile")->name("admin.api.save-file#post");
            Route::post('delete-file', "Admin\\Api\\Files@deleteFile")->name("admin.api.delete-file#post");

            Route::post('user-list', "Admin\\Api\\Users@userList")->name("admin.api.entity-list#post");
            Route::post('save-user', "Admin\\Api\\Users@saveUser")->name("admin.api.save-user#post");
            Route::post('delete-user', "Admin\\Api\\Users@deleteUser")->name("admin.api.delete-user#post");

            Route::post('behaviour-list', "Admin\\Api\\Behaviours@behaviourList")->name("admin.api.behaviour-list#post");
            Route::post('save-behaviour', "Admin\\Api\\Behaviours@saveBehaviour")->name("admin.api.save-behaviour#post");
            Route::post('delete-behaviour', "Admin\\Api\\Behaviours@deleteBehaviour")->name("admin.api.delete-behaviour#post");

            Route::post('dev-tools', "Admin\\Api\\Dev@devTools")->name("admin.api.dev-tools#post");
        });

        Route::group(['prefix' => 'upload'], function () {
            Route::post('entity', "Admin\\UploadController@entity")->name("admin.upload.entity#post");
            Route::post('show-content', "Admin\\UploadController@showContent")->name("admin.upload.show-content#post");
            Route::post('import-check', "Admin\\UploadController@importCheck")->name("admin.upload.importCheck#post");
            Route::post('import', "Admin\\UploadController@import")->name("admin.upload.import#post");
            Route::post('upload-file', "Admin\\UploadController@uploadFile")->name("admin.upload.upload-file#post");
        });

        Route::group(['prefix' => 'download'], function () {
            Route::post('entity', "Admin\\DownloadController@entity")->name("admin.download.entity#post");
            Route::post('exportMets', "Admin\\DownloadController@exportMets")->name("admin.download.exportMets#post");
            Route::post('exportCsv', "Admin\\DownloadController@exportCsv")->name("admin.download.exportCsv#post");
            //Route::get('export', "Admin\\DownloadController@export")->name("admin.download.export#get");
        });
    });
});

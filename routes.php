<?php

// session_start();
Color::initialize();

Route::get('', 'Overview');
// -------------API------------------------
// -----------get----------
Route::get('downloadFile', 'ApiGet', 'downloadFile');
Route::get('getFileList', 'ApiGet', 'getFileList');
Route::get('component/index.vue', 'ApiGet', 'getCmponent');
Route::get('js.get', 'ApiGet', 'getScript');
Route::get('css', 'ApiGet', 'css');
Route::get('js', 'ApiGet', 'js');
Route::get('pic.logo', 'ApiGet', 'getLogo');
Route::get('pic.loading', 'ApiGet', 'loadingPic');
Route::get('pic.load', 'ApiGet', 'getImage');
Route::get('header', 'ApiGet', 'getHeaderData');
Route::get('footer', 'ApiGet', 'getFooterData');
Route::get('test', 'ApiGet', 'test');
// -----------post
Route::post('post.changeLocale', 'ApiPost', 'changeLocale');
Route::post('uploadFile', 'ApiPost', 'uploadFile');
Route::post('removeFile', 'ApiPost', 'removeFile');
Route::run();

// ChromePhp::log($_GET);

// Make a request for a user with a given ID
// axios.get('/user?ID=12345')
//   .then(function (response) {
//     console.log(response);
//   })
//   .catch(function (error) {
//     console.log(error);
//   });

// // Optionally the request above could also be done as
// axios.get('/user', {
//     params: {
//       ID: 12345
//     }
//   })
//   .then(function (response) {
//     console.log(response);
//   })
//   .catch(function (error) {
//     console.log(error);
//   });

//   axios.post('/user', {
//     firstName: 'Fred',
//     lastName: 'Flintstone'
//   })
//   .then(function (response) {
//     console.log(response);
//   })
//   .catch(function (error) {
//     console.log(error);
//   });
//axios post parameter
// $body = file_get_contents('php://input');
//     $json=json_decode($_POST);

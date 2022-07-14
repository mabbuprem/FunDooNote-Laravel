<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\SendMailController;





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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
   // return $request->user();
//});
Route::post('register',[UserController::class,'register']);
Route::post('login', [UserController::class, 'login']);
Route::post('forgotPassword', [UserController::class, 'forgotPassword']);
Route::group(['middleware' => ['jwt.verify']], function() {
   Route::post('resetPassword', [UserController::class, 'resetPassword']);
   Route::get('logout', [UserController::class, 'logout']);
   Route::get('get_user', [UserController::class, 'get_user']);
   Route::get('/send-mail', [SendMailController::class, 'index'])->name('send.mail.index');
   //Route::post('resetPassword', [UserController::class, 'resetPassword']);

   Route::post('createNote', [NoteController::class, 'createNote']);
   Route::post('getNoteById',[NoteController::class,'getNoteById']);
Route::get('getAllNotes',[NoteController::class,'getAllNotes']);
Route::post('updateNoteById',[NoteController::class,'updateNoteById']);
Route::delete('deleteNoteById',[NoteController::class,'deleteNoteById']);

Route::post('createLabel', [LabelController::class, 'createLabel']);
Route::get('getLableById', [LabelController::class, 'getLableById']);
Route::get('getAllLabel', [LabelController::class, 'getAllLabel']);
Route::patch('updateLabelById', [LabelController::class, 'updateLabelById']);
Route::delete('deleteLabelById', [LabelController::class, 'deleteLabelById']);


});


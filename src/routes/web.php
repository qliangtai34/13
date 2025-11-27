<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

/*
|--------------------------------------------------------------------------
| Fortify 用（ログイン画面）
|--------------------------------------------------------------------------
*/
Route::get('/register', function () {
    return view('auth.register');
})->name('register');
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

/*
|--------------------------------------------------------------------------
| 勤怠管理（認証必須）
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () 
{

    Route::get('/attendance', [AttendanceController::class, 'index'])
        ->name('attendance.index');

    Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn'])
        ->name('attendance.clockIn');

    Route::post('/attendance/break-start', [AttendanceController::class, 'breakStart'])
        ->name('attendance.breakStart');

    Route::post('/attendance/break-end', [AttendanceController::class, 'breakEnd'])
        ->name('attendance.breakEnd');

    Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut'])
        ->name('attendance.clockOut');



    Route::get('/attendance/list', [AttendanceController::class, 'list'])->name('attendance.list');
    Route::get('/attendance/list/{year}/{month}', [AttendanceController::class, 'list'])->name('attendance.list.month');

    // 詳細画面
    Route::get('/attendance/detail/{date}', [AttendanceController::class, 'detail'])->name('attendance.detail');    
});


// 管理者ログイン画面
Route::get('/admin/login', function () {
    return view('admin.login'); // resources/views/admin/login.blade.php
})->name('admin.login');

// 管理者ログイン処理
Route::post('/admin/login', [App\Http\Controllers\Admin\AuthController::class, 'login'])
    ->name('admin.login.post');


Route::middleware(['auth', 'admin'])->group(function() {

    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');


    // 全ユーザー勤怠一覧
    Route::get('/admin/attendances', [\App\Http\Controllers\AdminAttendanceController::class, 'index'])
        ->name('admin.attendances');
});

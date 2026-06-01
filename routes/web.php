<?php

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin as Admin;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\KatalogController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/



Route::middleware(['guest'])->group(function () {});

Route::get('/', [FrontendController::class, 'index']);
Route::get('/home', [FrontendController::class, 'index'])->name('home');
Route::post('/send-attendance', [FrontendController::class, 'sendAttendance']);
Route::post('/check', [FrontendController::class, 'check'])->name('check');

Route::get('/admin/login', [Admin\AuthController::class, 'index'])->name('admin.login');
Route::post('/admin/sign-in', [Admin\AuthController::class, 'authenticate']);
Route::post('/admin/logout', [Admin\AuthController::class, 'logout']);

Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('admin.index');
    Route::put('settings/update-identity', [Admin\SettingController::class, 'update_identity']);
    Route::resource('settings', Admin\SettingController::class);
    Route::get('dashboard', [Admin\DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('dashboard-data', [Admin\DashboardController::class, 'getDashboardData']);
    Route::resource('users/roles', Admin\RoleController::class);
    Route::post('users/resetpassword', [Admin\UserController::class, 'resetpassword']);
    Route::resource('users', Admin\UserController::class);

    Route::get('holidays', [Admin\HolidayController::class, 'index']);
    Route::post('holidays', [Admin\HolidayController::class, 'store']);
    Route::post('holidays/delete', [Admin\HolidayController::class, 'delete']);
    Route::get('students', [Admin\StudentController::class, 'index']);
    Route::post('students/import-excel', [Admin\StudentController::class, 'import_excel']);
    Route::get('students/{id}', [Admin\StudentController::class, 'read']);
    Route::post('students/{id}', [Admin\StudentController::class, 'update']);
    Route::get('school-time', [Admin\SchoolTimeController::class, 'index']);
    Route::post('school-time', [Admin\SchoolTimeController::class, 'store']);
    Route::post('school-time/delete', [Admin\SchoolTimeController::class, 'delete']);
    Route::post('school-time/{id}', [Admin\SchoolTimeController::class, 'update']);
    Route::get('parent-report', [Admin\ParentreportController::class, 'index']);
    Route::post('parent-report', [Admin\ParentreportController::class, 'store']);
    Route::get('parent-report-data', [Admin\ParentreportController::class, 'getParentReportData']);


    Route::get('profile', [Admin\ProfileController::class, 'index']);
    Route::put('profile/{id}', [Admin\ProfileController::class, 'update']);
    Route::get('profile/edit', [Admin\ProfileController::class, 'edit']);
    Route::get('profile/edit-password', [Admin\ProfileController::class, 'edit_password']);
    Route::put('profile/change-password/{id}', [Admin\ProfileController::class, 'change_password']);
});

// require __DIR__ . '/auth.php';
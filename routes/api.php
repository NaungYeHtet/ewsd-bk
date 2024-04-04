<?php

use App\Http\Controllers\AcademicController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\IdeaCommentController;
use App\Http\Controllers\IdeaController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PasswordRuleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StatisticsController;
use App\Models\Academic;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Department;
use App\Models\Idea;
use App\Models\Report;
use App\Models\Staff;
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
require __DIR__.'/auth.php';

// Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::prefix('academics')->controller(ExportController::class)->group(function () {
    Route::get('/{academic}/export-data', 'data');
    Route::get('/{academic}/export-files', 'files');
});

Route::middleware(['auth:sanctum', 'verified', 'auth:staff'])->group(function () {
    Route::get('statistics', StatisticsController::class)->can('list statistics');
    Route::get('roles', RoleController::class);
    Route::prefix('profile')->controller(ProfileController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/update', 'update');
    });
    Route::prefix('ideas')->controller(IdeaController::class)->group(function () {
        Route::get('/', 'index')->can('viewAny', Idea::class);
        Route::post('/', 'store')->can('create', Idea::class);
        Route::get('/{idea}', 'show')->can('viewAny', Idea::class);
        Route::post('/{idea}/report', 'report')->can('create', Report::class);
        Route::put('/{idea}', 'update')->can('update', 'idea');
        Route::delete('/{idea}', 'destroy')->can('delete', 'idea');
        Route::get('/{idea}/react', 'react')->can('react', 'idea');
    });
    Route::prefix('notifications')->controller(NotificationController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/read', 'read');
        Route::get('/read-all', 'readAll');
    });
    Route::prefix('ideas/{idea}/comments')->controller(IdeaCommentController::class)->group(function () {
        Route::get('/', 'index')->can('viewAny', Comment::class);
        Route::post('/', 'store')->can('create', Comment::class);
        Route::put('/{comment}', 'update')->can('update', 'comment');
        Route::post('/{comment}/report', 'report')->can('create', Report::class);
        Route::delete('/{comment}', 'destroy')->can('delete', 'comment');
        Route::get('/{comment}/react', 'react')->can('react', 'comment');
    })->scopeBindings();
    Route::prefix('departments')->controller(DepartmentController::class)->group(function () {
        Route::get('/', 'index')->can('viewAny', Department::class);
        Route::post('/', 'store')->can('create', Department::class);
        Route::put('/{department}', 'update')->can('update', 'department');
        Route::delete('/{department}', 'destroy')->can('delete', 'department');
    });
    Route::prefix('categories')->controller(CategoryController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store')->can('create', Category::class);
        Route::put('/{category}', 'update')->can('update', 'category');
        Route::delete('/{category}', 'destroy')->can('delete', 'category');
    });
    Route::prefix('staffs')->controller(StaffController::class)->group(function () {
        Route::get('/', 'index')->can('viewAny', Staff::class);
        Route::post('/', 'store')->can('create', Staff::class);
        Route::put('/{staff}', 'update')->can('update', 'staff');
        Route::get('/{staff}/disable', 'disable')->can('update', 'staff');
        Route::get('/{staff}/enable', 'enable')->can('update', 'staff');
        Route::get('/{staff}/toggle-visibility/ideas', 'toggleIdeasVisibility')->can('update', 'staff');
        Route::get('/{staff}/toggle-visibility/comments', 'toggleCommentsVisibility')->can('update', 'staff');
    });
    Route::prefix('academics')->controller(AcademicController::class)->group(function () {
        Route::get('/', 'index')->can('viewAny', Academic::class);
        Route::post('/', 'store')->can('create', Academic::class);
        Route::put('/{academic}', 'update')->can('update', 'academic');
        Route::delete('/{academic}', 'destroy')->can('delete', 'academic');
    });
    Route::prefix('reports')->controller(ReportController::class)->group(function () {
        Route::get('/', 'index');
        Route::delete('/{report}', 'destroy')->can('delete', 'report');
        Route::get('/{report}/action', 'action')->can('action', Report::class);
    });
    Route::prefix('password-rules')->controller(PasswordRuleController::class)->group(function () {
        Route::get('/', 'index')->can('list password rule');
        Route::post('/', 'update')->can('update password rule');
    });
});

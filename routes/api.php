<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\IdeaController;
use App\Http\Controllers\PasswordRuleController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StaffController;
use App\Models\Category;
use App\Models\Department;
use App\Models\Idea;
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

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum', 'auth:staff'])->group(function () {
    Route::get('roles', RoleController::class);
    Route::prefix('ideas')->controller(IdeaController::class)->group(function () {
        Route::get('/', 'index')->can('viewAny', Idea::class);
        Route::post('/', 'store')->can('create', Idea::class);
        Route::put('/{idea}', 'update')->can('update', 'idea');
        Route::delete('/{idea}', 'destroy')->can('delete', 'idea');
    });
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
        // Route::delete('/{staff}', 'destroy')->can('delete', 'staff');
    });
    Route::prefix('password-rules')->controller(PasswordRuleController::class)->group(function () { 
        Route::get('/', 'index');
        Route::post('/', 'update');
    });
});

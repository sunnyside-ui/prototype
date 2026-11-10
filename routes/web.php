<?php

use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Middleware\RequireRole;
use Illuminate\Support\Facades\Route;

Route::view('/login', 'login')->middleware('guest')->name('login');
Route::post('/login', [SessionController::class, 'store'])->middleware(['guest', 'throttle:10,1'])->name('login.store');

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        abort_unless(in_array(auth()->user()->role, ['student', 'instructor', 'coordinator'], true), 403);
        return redirect()->route(auth()->user()->role.'.index');
    })->name('home');
    Route::post('/logout', [SessionController::class, 'destroy'])->name('logout');

    foreach (['student', 'instructor', 'coordinator'] as $role) {
        Route::prefix($role)->name($role.'.')->middleware(RequireRole::class.':'.$role)->group(function () use ($role) {
            Route::get('/', [WorkspaceController::class, 'home'])->name('index');
            Route::get('/sections/{section}', [WorkspaceController::class, 'section'])->name('section');
            Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
            Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
            Route::post('/profile/password', [ProfileController::class, 'password'])->name('profile.password');
            Route::get('/evaluations', [EvaluationController::class, 'index'])->name('grades');
            Route::get('/evaluations/{evaluation}', [EvaluationController::class, 'show'])->name('show');

            Route::get('/workspace/{module}', [WorkspaceController::class, 'show'])->name('module');
            Route::post('/workspace/{module}/records/{id?}', [WorkspaceController::class, 'save'])->whereNumber('id')->name('record.save');
            Route::get('/coursework/submissions/{id}/attachment', [WorkspaceController::class, 'attachment'])
                ->whereNumber('id')->name('work.attachment');
            if ($role === 'student') {
                Route::post('/applications', [WorkspaceController::class, 'apply'])->name('application.submit');
                Route::post('/coursework/submit', [WorkspaceController::class, 'submit'])->name('work.submit');
            }
            if ($role === 'coordinator') {
                Route::get('/academic-trackers', [\App\Http\Controllers\AcademicTrackerController::class, 'index'])->name('trackers.index');
                Route::post('/academic-trackers', [\App\Http\Controllers\AcademicTrackerController::class, 'store'])->name('trackers.store');
                Route::get('/academic-trackers/{tracker}', [\App\Http\Controllers\AcademicTrackerController::class, 'show'])->name('trackers.show');
                Route::post('/academic-trackers/{tracker}/{term}', [\App\Http\Controllers\AcademicTrackerController::class, 'update'])->name('trackers.update');
                Route::get('/academic-trackers/{tracker}/download', [\App\Http\Controllers\AcademicTrackerController::class, 'download'])->name('trackers.download');
                Route::post('/people/{id?}', [WorkspaceController::class, 'person'])->whereNumber('id')->name('person.save');
                Route::post('/enrollment/{id?}', [WorkspaceController::class, 'enroll'])->whereNumber('id')->name('enrollment.save');
            }

            if ($role === 'instructor') {
                Route::post('/evaluations/{evaluation}/submit', [EvaluationController::class, 'submit'])->name('submit');
            }

            if ($role === 'coordinator') {
                Route::post('/evaluations/{evaluation}/review', [EvaluationController::class, 'review'])->name('review');
            }
        });
    }
});








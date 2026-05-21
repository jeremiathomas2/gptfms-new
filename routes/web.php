<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\AuthController;

use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GroupSettingsController;
use App\Http\Controllers\UserImportController;
use App\Http\Controllers\NotificationController;

Route::middleware(['auth'])->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);

    Route::get('/groups', [GroupController::class, 'index'])->name('groups');
    Route::get('/my-group', [GroupController::class, 'myGroup'])->name('my_group');
    Route::get('/groups/settings', [GroupSettingsController::class, 'index'])->name('groups.settings');
    Route::get('/groups/{group}', [GroupController::class, 'show'])->name('groups.show');
    Route::post('/groups', [GroupController::class, 'store'])->name('groups.store');
    
    Route::post('/groups/settings', [GroupSettingsController::class, 'update'])->name('groups.settings.update');
    Route::post('/groups/settings/start', [GroupSettingsController::class, 'startCountdown'])->name('groups.settings.start');
    Route::post('/groups/settings/auto-form', [GroupSettingsController::class, 'autoFormGroups'])->name('groups.settings.auto_form');

    Route::get('/projects', [ProjectController::class, 'index'])->name('projects');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');

    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');

    Route::get('/messages', [MessageController::class, 'index'])->name('messages');
    Route::get('/messages/chats', [MessageController::class, 'getChats']);
    Route::get('/messages/{type}/{id}', [MessageController::class, 'getMessages']);
    Route::post('/messages', [MessageController::class, 'send'])->name('messages.send');

    Route::get('/reports', function () {
        return view('analytics.index');
    })->name('reports');

    Route::get('/evaluation', function () {
        return view('student.evaluation');
    })->name('evaluation');

    Route::get('/supervisor', function () {
        return view('supervisor.index');
    })->name('supervisor');

    Route::get('/admin', [AdminController::class, 'users'])->name('admin');
    Route::get('/admin/users/{user}', [AdminController::class, 'showUser'])->name('admin.users.show');
    Route::post('/admin/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::post('/admin/users/{user}/reset-password', [AdminController::class, 'resetPassword'])->name('admin.users.reset_password');
    Route::delete('/admin/users/{user}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');

    // Admin Group Management
    Route::get('/admin/groups', [AdminController::class, 'groups'])->name('admin.groups');
    Route::get('/admin/groups/search', [AdminController::class, 'searchGroups'])->name('admin.groups.search');
    Route::delete('/admin/groups/delete-all', [AdminController::class, 'deleteAllGroups'])->name('admin.groups.delete_all');
    Route::post('/admin/groups/{group}/update', [AdminController::class, 'updateGroup'])->name('admin.groups.update');
    Route::delete('/admin/groups/{group}', [AdminController::class, 'deleteGroup'])->name('admin.groups.delete');
    Route::post('/admin/groups/{group}/add-member', [AdminController::class, 'addGroupMember'])->name('admin.groups.add_member');
    Route::delete('/admin/groups/members/{member}', [AdminController::class, 'removeGroupMember'])->name('admin.groups.remove_member');
    Route::post('/admin/groups/{group}/assign-supervisor', [AdminController::class, 'assignSupervisor'])->name('admin.groups.assign_supervisor');

    Route::get('/settings', [ProfileController::class, 'settings'])->name('settings');
    Route::post('/settings/profile', [ProfileController::class, 'updateProfile'])->name('settings.profile');
    Route::post('/settings/security', [ProfileController::class, 'updateSecurity'])->name('settings.security');

    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/search', [AdminController::class, 'search'])->name('users.search');
    Route::get('/users/template/{type}', [UserImportController::class, 'downloadTemplate'])->name('users.template');
    Route::post('/users/import', [UserImportController::class, 'import'])->name('users.import');

    Route::get('/analytics', function () {
        return view('analytics.index');
    })->name('analytics');

    Route::get('/survey', [SurveyController::class, 'index'])->name('survey.index');
    Route::post('/survey', [SurveyController::class, 'store'])->name('survey.store');

    Route::get('/profile', [ProfileController::class, 'settings'])->name('profile');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    
    // Placeholder for password reset to avoid 500 error
    Route::get('/forgot-password', function () {
        return "Password reset functionality coming soon.";
    })->name('password.request');
});

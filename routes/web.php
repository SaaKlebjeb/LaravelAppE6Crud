<?php

use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/students');

Route::controller(StudentController::class)->prefix('students')->name('students.')->group(function () {
    Route::get('/', 'dashboard')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::post('/bulk-action', 'bulkAction')->name('bulk-action');
    Route::get('/export/csv', 'exportCsv')->name('export.csv');
    Route::get('/{student}', 'show')->name('show');
    Route::get('/{student}/edit', 'edit')->name('edit');
    Route::put('/{student}', 'update')->name('update');
    Route::delete('/{student}', 'destroy')->name('destroy');
    Route::patch('/{student}/restore', 'restore')->name('restore');
    Route::delete('/{student}/force-delete', 'forceDelete')->name('force-delete');
});

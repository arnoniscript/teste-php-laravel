<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ImportController;

Route::get('import/upload', [ImportController::class, 'showUploadForm'])->name('import.upload');
Route::post('import/import', [ImportController::class, 'import'])->name('import.import');
Route::get('import/queue', [ImportController::class, 'showQueueProcessing'])->name('import.queue');
Route::post('import/processQueue', [ImportController::class, 'processQueue'])->name('import.processQueue');
Route::get('/', function () {
    return view('welcome');
});

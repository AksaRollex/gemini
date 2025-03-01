<?php

use App\Http\Controllers\GeminiController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });


Route::get('/', [GeminiController::class, 'index'])->name('gemini.index');
Route::post('/gemini/generate-text', [GeminiController::class, 'generateText'])->name('gemini.generate-text');
Route::post('/gemini/analyze-image', [GeminiController::class, 'analyzeImage'])->name('gemini.analyze-image');
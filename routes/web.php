<?php

// Route::get('/', function () {
//     return view('welcome');
// });

use App\Http\Controllers\InvoicesController;
use Illuminate\Support\Facades\Route;

Route::get('{student}/invoice/generate', [InvoicesController::class, 'generatePdf'])->name('student.invoice.generate');

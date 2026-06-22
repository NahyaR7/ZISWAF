<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentWebhookController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Rute di dalam file ini secara otomatis DIBEBASKAN dari perlindungan 
| CSRF Token, sehingga sangat cocok untuk menerima data dari pihak 
| luar (seperti notifikasi/webhook dari server Midtrans).
|
*/

// Endpoint Webhook untuk menerima respon rahasia dari Payment Gateway (Midtrans)
Route::post('/midtrans/webhook', [PaymentWebhookController::class, 'handleMidtrans']);
<?php

use App\Http\Controllers\WebhookController;
use App\Http\Middleware\EnsureWooCommerceWebhookSignatureValid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/webhook', [WebhookController::class, 'handle'])
    ->middleware(EnsureWooCommerceWebhookSignatureValid::class);
<?php

use App\Http\Controllers\WebhookController;
use App\Http\Middleware\EnsureWooCommerceWebhookSignatureValid;
use App\Jobs\ExportArticleGroups;
use App\Jobs\ExportArticles;
use App\Jobs\ImportArticleGroups;
use App\Jobs\ImportArticles;

use Illuminate\Support\Facades\Route;

Route::post('/webhook', [WebhookController::class, 'handle'])
    ->middleware(EnsureWooCommerceWebhookSignatureValid::class);

Route::get('/import', function() {
    dispatch(new ImportArticleGroups());
    dispatch(new ImportArticles());

    return response()->json([
        'message' => 'Import started',
    ]);
});

Route::get('/export', function() {
    dispatch(new ExportArticleGroups());
    dispatch(new ExportArticles());;

    return response()->json([
        'message' => 'Export started',
    ]);
});
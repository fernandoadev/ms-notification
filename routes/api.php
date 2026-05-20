<?php

use App\Http\Controllers\CommunicationController;
use Illuminate\Support\Facades\Route;

Route::prefix('communications')->group(function () {
    Route::post('/', [CommunicationController::class, 'store'])->name('notifications.send');
});

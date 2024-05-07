<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrganizationFinderController;

Route::get('/', [OrganizationFinderController::class, 'index']);
Route::post('/', [OrganizationFinderController::class, 'show'])->name('organization.show');

<?php

use App\Http\Controllers\OrganizationFinderController;
use Illuminate\Support\Facades\Route;

Route::get('/', [OrganizationFinderController::class, 'index']);
Route::post('/', [OrganizationFinderController::class, 'show'])->name('organization.show');

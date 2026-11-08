<?php

use App\Http\Controllers\BranchMapController;
use Illuminate\Support\Facades\Route;

Route::get('/peta', [BranchMapController::class, 'index']);

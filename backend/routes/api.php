<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Internships\Http\Controllers\GetLocationController;
use Internships\Http\Controllers\DashboardController;

Route::middleware("auth:sanctum")->get("/user", fn(Request $request) => $request->user());
Route::get("/location/{address}", GetLocationController::class);

Route::middleware("auth")->group(function () {
    Route::get("/dashboard/stats", [DashboardController::class, "stats"]);
    Route::get("/dashboard/chart-data", [DashboardController::class, "chartData"]);
    Route::get("/dashboard/recent-activity", [DashboardController::class, "recentActivity"]);
});

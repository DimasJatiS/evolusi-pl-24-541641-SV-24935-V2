// routes/web.php
<?php

use Illuminate\Support\Facades\Route;

// Simple health-check — berguna untuk Docker health monitoring
Route::get('/', function () {
    return response()->json([
        'status' => 'ok',
        'app'    => config('app.name'),
        'env'    => config('app.env'),
    ]);
});

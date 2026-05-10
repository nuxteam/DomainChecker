<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DomainController;
use App\Http\Controllers\SettingsController;

Route::get('/', function () {
    return redirect()->route(auth()->check() ? 'dashboard' : 'login');
});

Route::middleware(['auth', 'throttle:80,1'])->group(function () {

    Route::get('/dashboard', function () {

        $domains = auth()->user()
            ->domains()
            ->with([
                'checks' => fn($q) => $q->latest()->limit(5),
                'latestCheck'
            ])
            ->latest()
            ->get();

        return view('dashboard', compact('domains'));

    })->name('dashboard');

    Route::post('/domains', [DomainController::class, 'store'])->name('domains.store');
    Route::get('/domains/{domain}/history', [DomainController::class, 'history'])->name('domains.history');
    Route::put('/domains/{domain}', [DomainController::class, 'update'])->name('domains.update');
    Route::delete('/domains/{domain}', [DomainController::class, 'destroy'])->name('domains.destroy');
    Route::post('/domains/{domain}/check', [DomainController::class, 'checkNow'])->name('domains.check');

    Route::get('/api/domains', function () {
        return auth()->user()
            ->domains()
            ->with('latestCheck')
            ->latest()
            ->limit(50)
            ->get();
    })->middleware('throttle:30,1');

    Route::get('/settings', [SettingsController::class, 'show']);
    Route::post('/settings', [SettingsController::class, 'update']);

});

require __DIR__.'/auth.php';
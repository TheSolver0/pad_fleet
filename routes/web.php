<?php

use App\Livewire\Auth\Login;
use App\Livewire\Portal\Dashboard\Index as DashboardIndex;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::middleware('guest')->group(function () {
    Route::get('/', Login::class)->name('login');
});

Route::post('/logout', function () {
    $user = Auth::user();
    if ($user) {
        \App\Services\AuditLogger::logLogout($user);
    }
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardIndex::class)->name('dashboard');
    Route::get('/vehicles', \App\Livewire\Portal\Vehicles\Index::class)->name('vehicles.index');
    Route::get('/brands', \App\Livewire\Portal\Brands\Index::class)->name('brands.index');
    Route::get('/vehicle-models', \App\Livewire\Portal\VehicleModels\Index::class)->name('vehicle-models.index');
    Route::get('/personnes', \App\Livewire\Portal\Personnes\Index::class)->name('personnes.index');
    Route::get('/affectations', \App\Livewire\Portal\Affectations\Index::class)->name('affectations.index');
    Route::get('/organisation/directions', \App\Livewire\Portal\Organisation\Directions\Index::class)->name('organisation.directions.index');
    Route::get('/organisation/departments', \App\Livewire\Portal\Organisation\Departments\Index::class)->name('organisation.departments.index');
    Route::get('/organisation/services', \App\Livewire\Portal\Organisation\OrgServices\Index::class)->name('organisation.services.index');
    // Route::get('/drivers', \App\Livewire\Portal\Drivers\Index::class)->name('drivers.index');
    // Route::get('/planning', \App\Livewire\Portal\Missions\Index::class)->name('missions.index');
    // Route::get('/demandeurs', \App\Livewire\Portal\Demandeurs\Index::class)->name('demandeurs.index');
    // Route::get('/assurances', \App\Livewire\Portal\Assurances\Index::class)->name('assurances.index');
    // Route::get('/sinistres', \App\Livewire\Portal\Sinistres\Index::class)->name('sinistres.index');
    // Route::get('/garages', \App\Livewire\Portal\Garages\Index::class)->name('garages.index');
    // Route::get('/repairs', \App\Livewire\Portal\Repairs\Index::class)->name('repairs.index');
    // Route::get('/reports', \App\Livewire\Portal\Reports\Index::class)->name('reports.index');
    Route::get('/audit', \App\Livewire\Portal\Audit\Index::class)->name('audit.index')->middleware('permission:audits');
});

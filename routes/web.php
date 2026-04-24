<?php

use App\Livewire\Auth\Login;
use App\Livewire\Portal\Drivers\Index as DriversIndex;
use App\Livewire\Portal\Drivers\DrivingLicenses;
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
    Route::get('/motorcycles', \App\Livewire\Portal\Motorcycles\Index::class)->name('motorcycles.index');
    Route::get('/carte-grises', \App\Livewire\Portal\CarteGrises\Index::class)->name('carte-grises.index');
    Route::get('/brands', \App\Livewire\Portal\Brands\Index::class)->name('brands.index');
    Route::get('/vehicle-models', \App\Livewire\Portal\VehicleModels\Index::class)->name('vehicle-models.index');
    Route::get('/personnes', \App\Livewire\Portal\Personnes\Index::class)->name('personnes.index');
    Route::get('/affectations', \App\Livewire\Portal\Affectations\Index::class)->name('affectations.index');
    Route::get('/organisation/directions', \App\Livewire\Portal\Organisation\Directions\Index::class)->name('organisation.directions.index');
    Route::get('/organisation/departments', \App\Livewire\Portal\Organisation\Departments\Index::class)->name('organisation.departments.index');
    Route::get('/organisation/services', \App\Livewire\Portal\Organisation\OrgServices\Index::class)->name('organisation.services.index');
    // Personnel
    Route::get('/drivers', DriversIndex::class)->name('drivers.index');
    Route::get('/drivers/driving-licenses', DrivingLicenses::class)->name('drivers.driving-licenses');
    Route::get('/drivers/documents', \App\Livewire\Portal\Drivers\Documents::class)->name('drivers.documents')->middleware('permission:suivi-assurances');
    Route::get('/drivers/leaves', \App\Livewire\Portal\Drivers\Leaves::class)->name('drivers.leaves');
    Route::get('/drivers/dispatches', \App\Livewire\Portal\Drivers\Dispatches::class)->name('drivers.dispatches');
    Route::get('/vehicles/control-sheets', \App\Livewire\Portal\Vehicles\ControlSheets::class)->name('vehicles.control-sheets');
    Route::get('/vehicles/control-sheets/{id}/pdf', \App\Http\Controllers\VehicleControlSheetPdfController::class)->name('vehicles.control-sheets.pdf');
    Route::get('/assureurs', \App\Livewire\Portal\Assureurs\Index::class)->name('assureurs.index');
    Route::get('/planning', \App\Livewire\Portal\Missions\Index::class)->name('missions.index');
    Route::get('/planning/synthese', \App\Livewire\Portal\Missions\Synthesis::class)->name('missions.synthesis');
    Route::get('/schedules', \App\Livewire\Portal\Schedules\Index::class)->name('schedules.index');
    Route::get('/demandeurs', \App\Livewire\Portal\Demandeurs\Index::class)->name('demandeurs.index');
    Route::get('/assurances', \App\Livewire\Portal\Assurances\Index::class)->name('assurances.index');
    Route::get('/sinistres', \App\Livewire\Portal\Sinistres\Index::class)->name('sinistres.index');
    Route::get('/garages', \App\Livewire\Portal\Garages\Index::class)->name('garages.index');
    Route::get('/repairs', \App\Livewire\Portal\Repairs\Index::class)->name('repairs.index');
    Route::get('/repairs/stock-usage', \App\Livewire\Portal\Repairs\StockUsage::class)->name('repairs.stock-usage')->middleware('permission:sorties-stock');
    Route::get('/repairs/documents', \App\Livewire\Portal\Repairs\Documents::class)->name('repairs.documents');
    Route::get('/mechanics', \App\Livewire\Portal\Mechanics\Index::class)->name('mechanics.index');
    Route::get('/mechanics/my-work', \App\Livewire\Portal\Mechanics\MyWork::class)->name('mechanics.my-work')->middleware('permission:enregistrement-interventions');
    Route::get('/diagnostics', \App\Livewire\Portal\Diagnostics\Index::class)->name('diagnostics.index');
    Route::get('/work-orders/create', [App\Http\Controllers\WorkOrderController::class, 'create'])->name('work-orders.create');
    Route::get('/work-orders', \App\Livewire\Portal\WorkOrders\Index::class)->name('work-orders.index');
    Route::get('/api/diagnostics/{id}/pdf', [App\Http\Controllers\API\DiagnosticPDFController::class, 'generatePDF']);
    Route::get('/api/work-orders/{id}/pdf', [App\Http\Controllers\API\WorkOrderPDFController::class, 'generatePDF']);
    Route::get('/api/work-orders/{id}/transfer-pdf', [App\Http\Controllers\API\WorkOrderPDFController::class, 'generateTransferPDF']);
    Route::get('/stock', \App\Livewire\Portal\Stock\Index::class)->name('stock.index')->middleware('permission:gestion-stock');
    Route::get('/stock/articles', \App\Livewire\Portal\Stock\Articles::class)->name('stock.articles')->middleware('permission:gestion-stock');
    Route::get('/stock/categories', \App\Livewire\Portal\Stock\Categories::class)->name('stock.categories')->middleware('permission:gestion-stock');
    Route::get('/stock/entries', \App\Livewire\Portal\Stock\Entries::class)->name('stock.entries')->middleware('permission:entrees-stock');
    Route::get('/stock/purchase-orders', \App\Livewire\Portal\Stock\PurchaseOrders::class)->name('stock.purchase-orders')->middleware('permission:entrees-stock');
    Route::get('/suppliers', \App\Livewire\Portal\Suppliers\Index::class)->name('suppliers.index');
    Route::get('/prestataire-evaluations', \App\Livewire\Portal\PrestataireEvaluations\Index::class)->name('prestataire-evaluations.index');
    Route::get('/reports', \App\Livewire\Portal\Reports\Index::class)->name('reports.index');
    Route::get('/reports/vehicle-consumption', \App\Livewire\Portal\Reports\VehicleConsumption::class)->name('reports.vehicle-consumption');
    Route::get('/reports/vehicle-reform', \App\Livewire\Portal\Reports\VehicleReform::class)->name('reports.vehicle-reform');
    Route::get('/reports/vehicle-consumption/export/excel', [\App\Http\Controllers\ReportExportController::class, 'vehicleConsumptionExcel'])->name('reports.vehicle-consumption.export.excel');
    Route::get('/reports/vehicle-consumption/export/pdf', [\App\Http\Controllers\ReportExportController::class, 'vehicleConsumptionPdf'])->name('reports.vehicle-consumption.export.pdf');
    Route::get('/reports/fleet/export/pdf', [\App\Http\Controllers\ReportExportController::class, 'fleetGlobalPdf'])->name('reports.fleet.export.pdf');

    Route::get('/reports/stock-movements/export/excel', [\App\Http\Controllers\ReportExportController::class, 'stockMovementsExcel'])->name('reports.stock-movements.export.excel');
    Route::get('/reports/stock-movements/export/pdf', [\App\Http\Controllers\ReportExportController::class, 'stockMovementsPdf'])->name('reports.stock-movements.export.pdf');

    Route::get('/reports/stock-per-vehicle/export/excel', [\App\Http\Controllers\ReportExportController::class, 'stockPerVehicleExcel'])->name('reports.stock-per-vehicle.export.excel');
    Route::get('/reports/stock-per-vehicle/export/pdf', [\App\Http\Controllers\ReportExportController::class, 'stockPerVehiclePdf'])->name('reports.stock-per-vehicle.export.pdf');

    Route::get('/reports/drivers/export/excel', [\App\Http\Controllers\ReportExportController::class, 'driversExcel'])->name('reports.drivers.export.excel');
    Route::get('/reports/drivers/export/pdf', [\App\Http\Controllers\ReportExportController::class, 'driversPdf'])->name('reports.drivers.export.pdf');

    Route::get('/reports/repairs/export/excel', [\App\Http\Controllers\ReportExportController::class, 'repairsExcel'])->name('reports.repairs.export.excel');
    Route::get('/reports/repairs/export/pdf', [\App\Http\Controllers\ReportExportController::class, 'repairsPdf'])->name('reports.repairs.export.pdf');

    Route::get('/reports/missions/export/excel', [\App\Http\Controllers\ReportExportController::class, 'missionsExcel'])->name('reports.missions.export.excel');
    Route::get('/reports/missions/export/pdf', [\App\Http\Controllers\ReportExportController::class, 'missionsPdf'])->name('reports.missions.export.pdf');

    Route::get('/reports/sinistres/export/excel', [\App\Http\Controllers\ReportExportController::class, 'sinistresExcel'])->name('reports.sinistres.export.excel');
    Route::get('/reports/sinistres/export/pdf', [\App\Http\Controllers\ReportExportController::class, 'sinistresPdf'])->name('reports.sinistres.export.pdf');
    Route::get('/audit', \App\Livewire\Portal\Audit\Index::class)->name('audit.index')->middleware('permission:audits');
    Route::get('/dashboard/trip-stats', [App\Http\Controllers\DashboardController::class, 'tripStats'])
    ->middleware(['auth'])
    ->name('dashboard.trip-stats');
Route::get('/visites-techniques', \App\Livewire\Portal\VehicleInspections\Index::class)
    ->middleware(['auth'])
    ->name('vehicle-inspections.index');
});

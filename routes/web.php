<?php

use App\Http\Controllers\AutomatedProcessController;
use App\Http\Controllers\MicrosoftController;
use App\Http\Controllers\ClosedAlertsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\GithubController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\OrchestratorConnectionController;
use App\Http\Controllers\OrchestratorConnectionTenantAlertController;
use App\Http\Controllers\PendingAlertsController;
use App\Http\Controllers\PropertyKeyController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UiPathAutomationCloudController;
use App\Http\Controllers\UiPathController;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('locale/{locale}', function ($locale) {
    //session()->put('locale', $locale);
    Cookie::queue(Cookie::forever('locale', $locale));

    return redirect()->back();
})->name('locale');

// orchestrator connections tenants webhook handler
Route::post(
    'configuration/orchestrator-connections/tenants/webhook-handler/{uuid}',
    [OrchestratorConnectionController::class, 'tenantsWebhookHandler']
)->name('configuration.orchestrator-connections.tenants.webhook-handler');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::controller(FileUploadController::class)->name('file-uploads.')->prefix('file-uploads')->group(function () {
        Route::post('/store/file', 'storeFile')->name('store.file');
        Route::post('/store/image', 'storeImage')->name('store.image');
    });

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::controller(PendingAlertsController::class)->name('pending-alerts.')->prefix('pending-alerts')->group(function () {
        Route::get('/', 'index')->name('index');
    });

    Route::resource('alerts', OrchestratorConnectionTenantAlertController::class, [
        'except' => [ 'index', 'create', 'store', 'update' ],
    ]);
    Route::controller(OrchestratorConnectionTenantAlertController::class)->name('alerts.')->prefix('alerts')->group(function () {
        Route::get('/{alert}/edit', 'edit')->name('edit');
        Route::post('/{alert}/read', 'read')->name('read');
        Route::post('/{alert}/lock', 'lock')->name('lock');
        Route::post('/{alert}/unlock', 'unlock')->name('unlock');
        Route::post('/{alert}/updateResolutionDetails', 'updateResolutionDetails')->name('update.resolution-details');
        Route::post('/bulk-read', 'bulkRead')->name('bulk-read');
        Route::post('/bulk-lock', 'bulkLock')->name('bulk-lock');
        Route::post('/bulk-unlock', 'bulkUnlock')->name('bulk-unlock');
    });
    
    Route::name('closed-alerts.')->prefix('closed-alerts')->group(function () {
        Route::get('/', [ClosedAlertsController::class, 'index'])->name('index');
    });

    Route::get('/tags', [TagController::class, 'getTags'])
        ->name('tags');

    Route::get(
        '/uipath/folders/{orchestrator_connection}/{orchestrator_connection_tenant}/{with_releases}/{with_machines}/{with_queue_definitions}',
        [UiPathController::class, 'folders'])
        ->name('uipath.folders');

    Route::name('configuration.')->prefix('configuration')->group(function () {
        Route::get('/', function () {
            return Inertia::render('Configuration/Index');
        })->name('index');
        Route::post(
            'generate/code',
            function () {
                return response()->json([
                    'code' => getAcronym(request('name'))
                ]);
            }
        )->name('generate-code');

        // orchestrator connections
        Route::resource('orchestrator-connections', OrchestratorConnectionController::class);
        Route::name('orchestrator-connections.')->prefix('orchestrator-connections')->group(function () {
            Route::post('/bulk-destroy', [OrchestratorConnectionController::class, 'bulkDestroy'])->name('bulk-destroy');
            Route::post('/verify/{orchestrator_connection?}', [OrchestratorConnectionController::class, 'verify'])->name('verify');
        });

        // automated processes
        Route::resource('automated-processes', AutomatedProcessController::class);
        Route::name('automated-processes.')->prefix('automated-processes')->group(function () {
            Route::post('/bulk-destroy', [AutomatedProcessController::class, 'bulkDestroy'])->name('bulk-destroy');
        });

        // property keys
        Route::resource('property-keys', PropertyKeyController::class);
        Route::post(
            'property-keys/bulk-destroy',
            [PropertyKeyController::class, 'bulkDestroy']
        )->name('property-keys.bulk-destroy');
    });
});

Route::name('auth.')->prefix('auth')->group(function () {
    if (env('AUTH_GITHUB_ENABLED', false)) {
        Route::controller(GithubController::class)->name('github.')->prefix('github')->group(function () {
            Route::get('', 'redirect')->name('redirect');
            Route::get('callback', 'callback')->name('callback');
        });
    }
    if (env('AUTH_GOOGLE_ENABLED', false)) {
        Route::controller(GoogleController::class)->name('google.')->prefix('google')->group(function () {
            Route::get('', 'redirect')->name('redirect');
            Route::get('callback', 'callback')->name('callback');
        });
    }
    if (env('AUTH_MICROSOFT_ENABLED', false)) {
        Route::controller(MicrosoftController::class)->name('microsoft.')->prefix('microsoft')->group(function () {
            Route::get('', 'redirect')->name('redirect');
            Route::get('callback', 'callback')->name('callback');
        });
    }
    if (env('AUTH_UIPATH_ENABLED', false)) {
        Route::controller(UiPathAutomationCloudController::class)->name('uipath.')->prefix('uipath')->group(function () {
            Route::get('', 'redirect')->name('redirect');
            Route::get('callback', 'callback')->name('callback');
        });
    }
});
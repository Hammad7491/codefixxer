<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\JobController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SiteController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SkillController;
use App\Http\Controllers\Auth\SocialController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ContractController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EducationController;
use App\Http\Controllers\Chatbot\ChatbotController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Chatbot\UserNameController;
use App\Http\Controllers\Admin\PurchaseOrderController;

// Public
Route::view('/', 'welcome');

Route::get('/login',      [AuthController::class, 'loginform'])->name('loginform');
Route::post('/login',     [AuthController::class, 'login'])->name('login');
Route::get('/register',   [AuthController::class, 'registerform'])->name('registerform');
Route::post('/register',  [AuthController::class, 'register'])->name('register');
Route::post('/logout',    [AuthController::class, 'logout'])->name('logout');
Route::view('/error',     'auth.errors.error403')->name('auth.error403');

// Social
Route::get('login/google',   [SocialController::class, 'redirectToGoogle'])->name('google.login');
Route::get('login/google/callback', [SocialController::class, 'handleGoogleCallback']);
Route::get('login/facebook', [SocialController::class, 'redirectToFacebook'])->name('facebook.login');
Route::get('login/facebook/callback', [SocialController::class, 'handleFacebookCallback']);

// Authenticated
Route::middleware('auth')->group(function () {



     // Admin area
     Route::prefix('admin')
          ->name('admin.')
          ->group(function () {




               Route::resource('clients', ClientController::class)
                    ->middleware('can:view clients');

               // Dashboard (single index action)
               Route::resource('dashboard', DashboardController::class)
                    ->only('index')
                    ->names(['index' => 'dashboard'])
                    ->middleware('can:view dashboard');

        
               Route::resource('users', UserController::class)

                    ->middleware('can:view users');

               // Roles CRUD
               Route::resource('roles', RoleController::class)

                    ->middleware('can:view roles');

               // Permissions CRUD
               Route::resource('permissions', PermissionController::class)

                    ->middleware('can:view permissions');
          });
});


route::prefix('admin')
    ->name('admin.')
    ->middleware('auth')
    ->group(function () {
        Route::resource('skills', SkillController::class)->except(['show']);
    });


    Route::prefix('admin')
    ->name('admin.')
    ->middleware('auth')
    ->group(function () {
        Route::resource('jobs', JobController::class); // full CRUD incl. show
    });



    Route::prefix('admin')
    ->name('admin.')
    ->middleware('auth')
    ->group(function () {
        Route::resource('projects', ProjectController::class); // full CRUD (index, create, store, show, edit, update, destroy)
    });


    Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::resource('projects', ProjectController::class);

    // stream/download project media safely
    Route::get('projects/{project}/media/{kind}/{index?}', [ProjectController::class, 'media'])
        ->whereIn('kind', ['video','doc','image'])
        ->name('projects.media');
});


Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    
    // Education CRUD
    Route::resource('educations', EducationController::class);
    
});



Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('languages', [LanguageController::class, 'index'])->name('languages.index');
    Route::get('languages/create', [LanguageController::class, 'create'])->name('languages.create');
    Route::post('languages', [LanguageController::class, 'store'])->name('languages.store');
    Route::get('languages/{language}/edit', [LanguageController::class, 'edit'])->name('languages.edit');
    Route::put('languages/{language}', [LanguageController::class, 'update'])->name('languages.update');
    Route::delete('languages/{language}', [LanguageController::class, 'destroy'])->name('languages.destroy');
});


Route::middleware(['auth'])->prefix('admin')->as('admin.')->group(function () {
    // CRUD: index, create, store, show, edit, update, destroy
    Route::resource('invoices', InvoiceController::class);
});

Route::middleware(['auth'])->prefix('admin')->as('admin.')->group(function () {
    Route::resource('invoices', InvoiceController::class);

    // Optional helpers
    Route::get('invoices/{invoice}/print',    [InvoiceController::class, 'print'])->name('invoices.print');
    Route::get('invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download'); // e.g., PDF
    Route::post('invoices/{invoice}/send',    [InvoiceController::class, 'send'])->name('invoices.send');         // email/share
    Route::post('invoices/{invoice}/duplicate',[InvoiceController::class, 'duplicate'])->name('invoices.duplicate');
});



Route::middleware(['auth'])->prefix('admin')->as('admin.')->group(function () {
    // Contracts CRUD
    Route::resource('contracts', ContractController::class);
});
